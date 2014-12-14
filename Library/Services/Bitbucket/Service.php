<?php
namespace IssueCreator\Services\Bitbucket;
require_once APPLICATION_PATH.DIRECTORY_SEPARATOR.'Library'.DIRECTORY_SEPARATOR.'Services'.DIRECTORY_SEPARATOR.'ServicesAbstract.php';
/**
 * Class to interact with Bitbucket API
 * Uses bitbucket API 1.0
 * @author Lokendra Shukla
 *
 */
use IssueCreator\Services\ServicesAbstract;
class Service extends ServicesAbstract {

    public $apiUrl = 'https://bitbucket.org/api/1.0/repositories';

    /**
     * Bitbucket Constructor
     *
     * @param Issue Class Object
     * @return Void
     */
    public function __construct(\IssueCreator\IssueManager $client) {
        parent::__construct($client);
    }

    /**
     * Bitbucket setAuth
     *
     * @param Curl Handler Object
     * @return Void
     */
    public function setAuth($ch) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode("{$this->_username}:{$this->_password}")));
    }

    /**
     * Bitbucket createIssue
     * @link https://confluence.atlassian.com/display/BITBUCKET/issues+Resource#issuesResource-POSTanewissue
     * @param Issue Title and Description String
     * @return CURL response from the API
     */
    public function createIssue($title, $description = null) {

        $data = array();
        $data['title'] = $title;
        if (!is_null($description))
            $data['content'] = $description;

        $data = http_build_query($data);
        $url = sprintf('%s/%s/%s/issues', $this->apiUrl, $this->_owner, $this->_repo);
        return $this->_client->request($url, 'POST', $data);
    }

    /**
     * Bitbucket parseResponse
     *
     * @param CURL response from the API
     * @return API parsed response String
     */
    public function parseResponse($response) {
        ob_start();
        echo "==========================================================================\n";
        if (isset($response['response']->resource_uri)) {
            echo "Issue URL: \t\t" . $response['response']->resource_uri . "\n";
            echo "Issue Title: \t\t" . $response['response']->title . "\n";
            echo "Issue Created at: \t" . $response['response']->utc_created_on . "\n";
        } else {
            throw new \Exception("No response from API. Entered data may be incorrect");
        }
        echo "==========================================================================\n";
        $data = ob_get_contents();
        ob_get_clean();
        return $data;
    }

}