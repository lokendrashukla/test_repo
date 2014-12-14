<?php
namespace IssueCreator\Services\Github;
require_once APPLICATION_PATH.DIRECTORY_SEPARATOR.'Library'.DIRECTORY_SEPARATOR.'Services'.DIRECTORY_SEPARATOR.'ServicesAbstract.php';

/**
 * Class to interact with Github API
 * Uses github API
 * @author Lokendra Shukla
 *
 */
use IssueCreator\Services\ServicesAbstract;
class Service extends ServicesAbstract {

    public $apiUrl = 'https://api.github.com';

    /**
     * GitHub Constructor
     *
     * @param Issue Class Object
     * @return Void
     */
    public function __construct(\IssueCreator\IssueManager $client) {
        parent::__construct($client);
    }

    /**
     * GitHub setAuth
     * @link https://developer.github.com/v3/auth/#basic-authentication
     * @param Curl Handler Object
     * @return Void
     */
    public function setAuth($ch) {
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->_username}:{$this->_password}");
    }

    /**
     * GitHub createIssue
     * @link https://developer.github.com/v3/issues/#create-an-issue
     * @param Issue Title and Description String
     * @return CURL response from the API
     */
    public function createIssue($title, $description = null) {

        $data = array();
        $data['title'] = $title;
        if (!is_null($description))
            $data['body'] = $description;

        $data = json_encode($data);
        $url = sprintf('%s/repos/%s/%s/issues', $this->apiUrl, $this->_owner, $this->_repo);
        return $this->_client->request($url, 'POST', $data);
    }

    /**
     * GitHub parseResponse
     *
     * @param CURL response from the API
     * @return API parsed response String
     */
    public function parseResponse($response) {
        ob_start();
        echo "==========================================================================\n";
        if (isset($response['response']->message)) {
            echo 'API Message: ' . $response['response']->message . "\n";
        } else if (isset($response['response']->id)) {
            echo "ID: \t\t\t" . $response['response']->id . "\n";
            echo "Issue URL: \t\t" . $response['response']->url . "\n";
            echo "Issue Title: \t\t" . $response['response']->title . "\n";
            echo "Issue Created at: \t" . $response['response']->created_at . "\n";
        } else {
            throw new \Exception("No response from API. Entered data may be incorrect");
        }
        echo "==========================================================================\n";
        $data = ob_get_contents();
        ob_get_clean();
        return $data;
    }

}