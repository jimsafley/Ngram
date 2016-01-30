<?php
class NgramCorpus extends Omeka_Record_AbstractRecord
{
    public $id;
    public $name;
    public $query;
    public $sequence_element_id;
    public $sequence_type;
    public $sequence_range;
    public $items;

    protected $_related = array(
        'SequenceElement' => 'getSequenceElement',
        'SequenceType' => 'getSequenceType',
        'Items' => 'getItems',
    );

    /**
     * Get the element containing sequence text.
     *
     * @return Element
     */
    public function getSequenceElement()
    {
        return $this->getTable('Element')->find($this->sequence_element_id);
    }

    /**
     * Get sequence type object.
     *
     * @return Ngram_SequenceType_SequenceTypeInterface
     */
    public function getSequenceType()
    {
        return $this->getTable()->getSequenceType($this->sequence_type);
    }

    /**
     * Get all item IDs in this corpus, valid and invalid.
     *
     * @return array
     */
    public function getItems()
    {
        return json_decode($this->items, true);
    }

    /**
     * Can a user validate items?
     *
     * @return bool
     */
    public function canValidateItems()
    {
        return (bool) $this->Items;
    }

    public function getRecordUrl($action = 'show')
    {
        return array('controller' => 'ngram', 'action' => $action, 'id' => $this->id);
    }

    protected function _validate() {
        if ('' === trim($this->name)) {
            $this->addError('name', 'A name is required');
        }
        if (!$this->getTable('Element')->exists($this->sequence_element_id)) {
            $this->addError('Sequence Element', 'Invalid sequence element');
        }
        if (!$this->SequenceType) {
            $this->addError('Sequence Type', 'Invalid sequence type');
        }
    }
}
