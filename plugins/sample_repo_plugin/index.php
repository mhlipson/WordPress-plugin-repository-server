<?php
/*
Plugin Name: Repo Test
Plugin URI: https://github.com/mhlipson/WordPress-plugin-repository-server
Description: This is an example plugin demonstrating a universal plugin repository. 
Version: 0.9
Author: Michael Lipson
Author URI: http://www.twitter.com/michaellipson
License: GPLv2 (I guess?)
*/

define('PR_SERVER','http://repo.server.dev'); // Define alternate server
define('PR_PREFIX','SAMPLE'); // Define function prefix to avoid conflicts.

include('plugin.repo.php');

?>