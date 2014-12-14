<?php
/**
 * 
 * Create issues on github and bitbucket
 * Examples :- php index.php create-issue -u lokendrashukla https://github.com/lokendrashukla/issueCreator "Issue 1" "This is a sample issue."
 * Examples :- php index.php create-issue -u lokendrashukla https://bitbucket.org/lokendrashukla/issueCreator "Issue 1" "This is a sample issue."
 * @author Lokendra Shukla
 */
define('APPLICATION_PATH', dirname(__FILE__));

require APPLICATION_PATH . DIRECTORY_SEPARATOR . 'Library'.DIRECTORY_SEPARATOR.'IssueManager.php';

try {

    if (PHP_SAPI != 'cli') {
        throw new \Exception('This script will run from the command line!');
    }

    // unset the first argument which conatains file name
    array_shift($argv);

    // create the issue object and call the execute
    $issue = new IssueCreator\IssueManager($argv);
    echo $issue->execute();
    
} catch (Exception $e) {
    echo "Error : " . $e->getMessage() . "\n";
}