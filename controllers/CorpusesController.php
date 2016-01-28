<?php
class Ngram_CorpusesController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('NgramCorpus');
    }

    public function addAction()
    {
        if ($this->getRequest()->isPost()) {
            $this->getRequest()->setPost('text_element_id', get_option('ngram_text_element_id'));
        }
        $this->view->elementOptions = get_db()->getTable('NgramCorpus')->getElementsForSelect();
        parent::addAction();
        $this->view->corpus = $this->view->ngram_corpu; // correct poor inflection
    }

    protected function _getAddSuccessMessage($record)
    {
        return sprintf('The "%s" corpus was sucessfully added.', $record->name);
    }
}
