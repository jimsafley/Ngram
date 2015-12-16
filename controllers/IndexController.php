<?php
class Ngram_IndexController extends Omeka_Controller_AbstractActionController
{
    public function indexAction()
    {
        $args = array();
        Omeka_Job_Process_Dispatcher::startProcess('Ngram_StoreNgramsProcess', null, $args);
    }
}
