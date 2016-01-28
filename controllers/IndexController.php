<?php
class Ngram_IndexController extends Omeka_Controller_AbstractActionController
{
    public function indexAction()
    {
        $this->_helper->redirector(null, 'corpuses');
    }
}
