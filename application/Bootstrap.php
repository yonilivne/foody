<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initResources()
    {
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => APPLICATION_PATH,
            'namespace' => '',
        ));
        $resourceLoader->addResourceType('form', 'forms/', 'Form')
            ->addResourceType('model', 'models/', 'Model');
    }

    protected function _initConfig()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
        Zend_Registry::set('config', $config);
    }

}

