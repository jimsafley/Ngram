<?php
class NgramCorpus extends Omeka_Record_AbstractRecord
{
    public $id;
    public $name;
    public $query;
    public $text_element_id;
    public $sequence_element_id;
    public $sequence_type;
    public $sequence_range;
    public $items;

    public function getTextElement()
    {
        return $this->getTable('Element')->find($this->text_element_id);
    }

    public function getSequenceElement()
    {
        return $this->getTable('Element')->find($this->sequence_element_id);
    }

    public function getItems()
    {
        return json_decode($this->items, true);
    }

    public function getRecordUrl($action = 'show')
    {
        return array('controller' => 'ngram', 'action' => $action, 'id' => $this->id);
    }

    public function canGenerateCorpusNgrams()
    {
        // all items in $this->items are in the item_ngrams table
    }

    protected function _validate() {
        $db = $this->getDb();
        $name = trim($this->name);
        if ('' === $name) {
            $this->addError('name', 'A name is required');
        }
        $elementId = $this->sequence_member_element_id;
        if (!$db->getTable('Element')->exists($this->sequence_element_id)) {
            $this->addError('Sequence Element', 'An element is required');
        }
    }
}
