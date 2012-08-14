<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/../public'));

defined('IMAGES_PATH')
    || define('IMAGES_PATH', PUBLIC_PATH . '/images');

switch(APPLICATION_ENV){
    case "development":
        $zend_dir = realpath(APPLICATION_PATH. "/../../");
        break;
    default:
        $zend_dir = realpath(APPLICATION_PATH. "/../../zend/v1.11.11/library");
        break;
}

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    $zend_dir,
    get_include_path(),
)));

require_once realpath(APPLICATION_PATH . '/../library/AjaxFileUpload.php');
require_once realpath(APPLICATION_PATH . '/../library/wideimage/WideImage.php');
/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()->run();