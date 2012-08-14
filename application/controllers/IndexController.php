<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $postMapper = new Model_PostMapper();
        $this->view->entries = $postMapper->fetchAll();

    }


}

