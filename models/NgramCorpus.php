<?php
class NgramCorpus extends Omeka_Record_AbstractRecord
{
    public $id;
    public $name;
    public $query;
    public $text_element_id;
    public $sequence_member_element_id;
    public $sequence_member_pattern;
    public $sequence_member_range;
    public $valid_items;
    public $invalid_items;

    public function getTextElement()
    {
        return $this->getTable('Element')->find($this->text_element_id);
    }

    public function getSequenceMemberElementId()
    {
        return $this->getTable('Element')->find($this->sequence_member_element_id);
    }

    public function getValidItems()
    {
        return json_decode($this->valid_items, true);
    }

    public function getInvalidItems()
    {
        return json_decode($this->invalid_items, true);
    }

    public function getRecordUrl($action = 'show')
    {
        return array('controller' => 'ngram', 'action' => $action, 'id' => $this->id);
    }

    public function canValidateItems()
    {
        // corpus ngrams have not yet been generated / process is not running
    }

    public function canGenerateCorpusNgrams()
    {
        // all items in $this->valid_items are in the item_ngrams table
    }

    protected function _validate() {
        $db = $this->getDb();
        $name = trim($this->name);
        if ('' === $name) {
            $this->addError('name', 'A name is required');
        }
        $elementId = $this->sequence_member_element_id;
        if (!$db->getTable('Element')->exists($this->sequence_member_element_id)) {
            $this->addError('Sequence Element', 'An element is required');
        }
        $pattern = trim($this->sequence_member_pattern);
        if ('' === $pattern) {
            $this->addError('Sequence Pattern', 'A pattern is required');
        }
    }
}
