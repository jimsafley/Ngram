<?php
class Ngram_IndexController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('NgramCorpus');
    }

    public function addAction()
    {
        parent::addAction();

        $table = $this->_helper->db;
        $this->view->sequenceTypeOptions = $table->getTable()->getSequenceTypesForSelect();
        $this->view->sequenceElementOptions = $table->getTable()->getElementsForSelect();
        $this->view->corpus = $this->view->ngram_corpu; // correct poor inflection
    }

    /**
     * Set items in corpus.
     *
     * @param Omeka_Record_AbstractRecord $corpus
     */
    protected function _redirectAfterAdd($corpus)
    {
        parse_str($corpus->query, $query);
        // Items must be described by the corpus sequence element.
        $query['advanced'][] = array(
            'element_id' => $corpus->sequence_element_id,
            'type' => 'is not empty',
        );
        // Items must be described by the corpus text element.
        $query['advanced'][] = array(
            'element_id' => get_option('ngram_text_element_id'),
            'type' => 'is not empty',
        );

        $table = $this->_helper->db;
        $items = $table->getTable('Item')->findBy($query);
        $itemIds = array();
        foreach ($items as $item) {
            $itemIds[] = $item->id;
        }

        $corpus->items = json_encode($itemIds);
        $corpus->save(false);

        parent::_redirectAfterAdd($corpus);
    }

    public function validateAction()
    {
        $table = $this->_helper->db;
        $db = $table->getDb();
        $corpus = $table->findById();

        // Query the database directly to get sequence element text. This
        // reduces the overhead that would otherwise be required to cache all
        // element texts.
        $sql = sprintf(
        'SELECT i.id, et.text
        FROM %s i JOIN %s et
        ON i.id = et.record_id
        WHERE i.id IN (%s)
        AND et.element_id = %s
        GROUP BY i.id',
        $db->Item,
        $db->ElementText,
        $db->quote(json_decode($corpus->items, true)),
        $db->quote($corpus->sequence_element_id));
        $sequenceTexts = $db->fetchPairs($sql);

        // Validate the sequence text.
        $validator = $table->getCorpusValidator($corpus->sequence_type);
        foreach ($sequenceTexts as $id => $text) {
            $validator->addItem($id, $text);
        }

        // Prepare valid items.
        $validItems = $validator->getValidItems();
        natcasesort($validItems);
        foreach ($validItems as $id => $sequenceMember) {
            $validItems[$id] = array(
                'member' => $sequenceMember,
                'text' => $sequenceTexts[$id],
            );
        }

        // Prepare invalid items.
        $invalidItems = array();
        foreach ($validator->getInvalidItems() as $id) {
            $invalidItems[$id] = $sequenceTexts[$id];
        }
        natcasesort($invalidItems);

        $this->view->corpus = $corpus;
        $this->view->validItems = $validItems;
        $this->view->invalidItems = $invalidItems;
    }

    protected function _getAddSuccessMessage($record)
    {
        return sprintf('The "%s" corpus was sucessfully added.', $record->name);
    }
}
