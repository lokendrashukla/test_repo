<?php
/**
 * This class decides which service to call based on repo URL supplied.
 * @author Lokendra Shukla
 *
 */
Namespace IssueCreator;
class IssueManager {
	
	/**
	 *
	 * @var method String
	 */
	protected $_serviceMethod;
	
	/**
	 *
	 * @var Username String
	 */
	protected $_username;
	
	/**
	 *
	 * @var Password String
	 */
	protected $_password;
	
	/**
	 *
	 * @var URL String
	 */
	protected $_repository;
	
	/**
	 *
	 * @var Issue Title String
	 */
	protected $_issueTitle;
	
	/**
	 *
	 * @var Issue Description String
	 */
	protected $_issueDescription;
	
	/**
	 *
	 * @var service Object
	 */
	protected $_service;
	
	/**
	 * @var allowedMethods
	 */
	protected $_allowedMethods = array('create-issue' => 'createIssue');
	
	/**
	 * Issue Constructor
	 * @param array Arguments passed to the command line
	 * @return Void
	 */
	public function __construct($params) {
		$this->_parseArguments ( $params );
	}
	
	/**
	 * Issue _initService
	 *
	 * @param String $name
	 *        	Initialize service
	 * @return Void
	 */
	protected function _initService($name) {
		$filename = APPLICATION_PATH .DIRECTORY_SEPARATOR. 'Library' .DIRECTORY_SEPARATOR. 'Services' .DIRECTORY_SEPARATOR. $name .DIRECTORY_SEPARATOR.'Service.php';
		if (file_exists ( $filename )) {
			require_once $filename;
		} else {
			throw new \Exception ( 'Unsupported Repository!' );
		}
		$serviceName = "IssueCreator\\Services\\".$name."\\Service";
		$this->_service = new  $serviceName( $this );
	}
	
	/**
	 * Issue _parseArguments
	 * This function parse the arguments supplied from the command line
	 *
	 * @param Array $params        	
	 * @return Void
	 */
	protected function _parseArguments($params) {
		if (isset ( $params [0] ) && isset($this->_allowedMethods[$params[0]])) {
			$this->_serviceMethod = $this->_allowedMethods[$params[0]];
		} else {
			throw new \Exception ( 'Method is undefined!' );
		}
		
		if (isset ( $params [1] ) && isset ( $params [2] )) {
			if ($params [1] == '-u') {
				$this->_username = $params [2];
			}
			else {
				throw new \Exception ( 'Unknown option!' );
			}
		}
		
		if (empty ( $this->_username )) {
			throw new \Exception ( 'Username is undefined!' );
		}
		
		if (empty ( $this->_password )) {
			if (PHP_OS == 'WINNT') {
				echo 'Enter your password: ';
				$this->_password = stream_get_line(STDIN, 1024, PHP_EOL);
			} else {
				$this->_password = readline('Enter your password : ');
			}
		}
		if(empty($this->_password)){
			throw new \Exception ( 'Password not entered!' );
		}
		if (isset ( $params [3] )) {
			$this->_repository = $params [3];
		} else {
			throw new \Exception ( 'Repository URL is undefined!' );
		}
		
		if (isset ( $params [4] )) {
			$this->_issueTitle = $params [4];
		} else {
			throw new \Exception ( 'Issue Title is undefined!' );
		}
		
		if (isset ( $params [5] )) {
			$this->_issueDescription = $params [5];
		} else {
			$this->_issueDescription = '';
		}
	}
	
	/**
	 * Action execute
	 *
	 * @return response string
	 */
	public function execute() {
		$repo = parse_url ( $this->_repository );
		
		$url_services = parse_url ( $this->_repository );
		
		$host = explode ( '.', $url_services ['host'] );
		$class = ucwords ( strtolower ( $host [count ( $host ) - 2] ) );
		
		//Initialize the service
		$this->_initService ( $class );
		
		//Setting variables in the service
		$this->_service->setRepository ( $this->_repository )
		->setUsername ( $this->_username )
		->setPassword ( $this->_password );
		
		$data = false;

		$methodToCall = $this->_serviceMethod;
		return $this->_service->$methodToCall( $this->_issueTitle, $this->_issueDescription );
	}
	
	/**
	 * Issue request
	 * do a request and return String
	 *
	 * @param string $url        	
	 * @param string $method
	 *        	GET POST
	 * @param array $data        	
	 * @return response String
	 */
	public function request($url, $method, $data) {
		$ch = curl_init ();
		
		$this->_service->setAuth ( $ch );
		
		curl_setopt ( $ch, CURLOPT_USERAGENT, "IssueCreator.issuecreator.daemon" );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, true );
		if($method == 'GET') {
			curl_setopt ( $ch, CURLOPT_HTTPGET, true );
			if (count ( $data ))
			$url .= '?' . http_build_query ( $data );
		}
		elseif($method == 'POST'){
			curl_setopt ( $ch, CURLOPT_POST, true );
			if (count ( $data ))
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		}
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		
		$response = array (
				'response' => json_decode ( curl_exec ( $ch ) ),
				'error' => curl_error ( $ch ) 
		);
		curl_close ( $ch );
		
		return $this->_service->parseResponse ( $response );
	}
}