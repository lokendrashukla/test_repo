IssueCreator
============

This project can be used to create issues in Git & Bitbucket.

Dependencies
============

PHP > 5.4.0
PHP CURL Extension

Requirements
============
A Repository URL either Github or Bitbucket
An account on Github/Bitbucket.

Installation
============

Download the code from the GIT. Use:
$ git clone https://github.com/IssueCreatorshukla/issuecreator.git <destination folder>


From the command line, run the following command

$ cd <project_folder>

On GIT
======
$ php index.php create-issue -u <git-username> http://github.com/:username/:repo "<issue-title>" "<issue-description>"

On BitBucket
============
$ php index.php create-issue -u <bitbucket-username> http://bitbucket.org/:owner/:repo "<issue-title>" "<issue-description>"

Enter Github/Bitbucket password on the prompt! If everything is right issue would be created.

Notes
=====
Please note that repository URL must be public and it should be configured such that others can create issues.

