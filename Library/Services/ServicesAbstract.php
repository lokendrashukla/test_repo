<?php
/**
 * Services Abstract Class
 * @author Lokendra Shukla
 *
 */
namespace IssueCreator\Services;

use IssueCreator;
abstract class ServicesAbstract {

    /**
     * @var _username String
     */
    protected $_username;

    /**
     * @var _password String
     */
    protected $_password;

    /**
     * @var Repository Owner String
     */
    protected $_owner;

    /**
     * @var Repository Name String
     */
    protected $_repo;

    /**
     * @var IssuesClient Object
     */
    protected $_client;

    /**
     * Services Constructor
     *
     * @return Void
     */
    public function __construct(IssueCreator\IssueManager $client) {
        $this->_client = $client;
    }

    /**
     * setRepository
     *
     * @return Self Instance
     */
    public function setRepository($repository) {
        $url_components = parse_url($repository);

        if (!empty($url_components['path']) && $url_components['path'] !== '/') {
            $path = explode('/', $url_components['path']);
            $this->_owner = $path[1];

            if (isset($path[2])) {
                $this->_repo = $path[2];
            } else {
                throw new \Exception("Repository name not found");
            }
        } else {
            throw new \Exception("Owner and Repository name not found");
        }

        return $this;
    }

    /**
     * setUsername
     *
     * @return Self Instance
     */
     
    public function setUsername($username) {
        $this->_username = $username;
        return $this;
    }

    /**
     * setPassword
     *
     * @return Self Instance
     */
    public function setPassword($password) {
        $this->_password = $password;
        return $this;
    }

    /**
     * setAuth
     *
     * @param Curl Handler Object
     */
    abstract public function setAuth($curl_handler);

    /**
     * createIssue
     *
     * @param Issue Title and Description String
     */
    abstract public function createIssue($title, $description);

    /**
     * parseResponse
     *
     * @param Response from the API
     */
    abstract public function parseResponse($response);
}