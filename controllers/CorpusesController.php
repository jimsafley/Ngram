<?php
class Ngram_CorpusesController extends Omeka_Controller_AbstractActionController
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

    public function validateItemsAction()
    {
        $table = $this->_helper->db;
        $db = $table->getDb();
        $corpus = $table->find($this->getParam('id'));

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
        $items = $db->fetchAll($sql);

        // Validate the sequence text.
        $sequenceType = $corpus->SequenceType;
        $sequenceType->setItems($items);
        $validItems = $sequenceType->getValidItems();
        $invalidItems = $sequenceType->getInalidItems();

        // Sort the valid and invalid item arrays.
        usort($validItems, function($a, $b) {
            if ($a['sequence_member'] === $b['sequence_member']) {
                return 0;
            }
            return $a['sequence_member'] < $b['sequence_member'] ? -1 : 1;
        });
        usort($invalidItems, function($a, $b) {
            return strcmp($a['sequence_text'], $b['sequence_text']);
        });

        $this->view->corpus = $corpus;
        $this->view->validItems = $validItems;
        $this->view->invalidItems = $invalidItems;
    }

    protected function _getAddSuccessMessage($record)
    {
        return sprintf('The "%s" corpus was sucessfully added.', $record->name);
    }
}
