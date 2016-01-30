<?php
class Table_NgramCorpus extends Omeka_Db_Table
{
    /**
     * @var array Register of available sequence types.
     */
    protected $_sequenceTypes = array(
        'Ngram_SequenceType_Year',
        'Ngram_SequenceType_Month',
        'Ngram_SequenceType_Day',
    );

    /**
     * Get a sequence type object by class name.
     *
     * @param string $class
     * @return Ngram_SequenceType_SequenceTypeInterface
     */
    public function getSequenceType($class)
    {
        return class_exists($class) ? new $class : null;
    }

    /**
     * Get all sequence type objects, keyed by class name.
     *
     * @return array
     */
    public function getSequenceTypes()
    {
        $types = array();
        foreach ($this->_sequenceTypes as $class) {
            $types[$class] = new $class;
        }
        return $types;
    }

    /**
     * Get sequence types array used as select options.
     *
     * @return array
     */
    public function getSequenceTypesForSelect()
    {
        $options = array('' => 'Select Below');
        foreach ($this->getSequenceTypes() as $sequenceType) {
            $options[get_class($sequenceType)] = $sequenceType->getLabel();
        }
        return $options;
    }

    /**
     * Find all elements and their element sets.
     *
     * @return array
     */
    public function findElements()
    {
        $db = $this->getDb();
        $select = $db->select()
            ->from(
                array('element_sets' => $db->ElementSet),
                array('element_set_name' => 'element_sets.name')
            )->join(
                array('elements' => $db->Element),
                'element_sets.id = elements.element_set_id',
                array('element_id' =>'elements.id',
                'element_name' => 'elements.name')
            )->joinLeft(
                array('item_types_elements' => $db->ItemTypesElements),
                'elements.id = item_types_elements.element_id',
                array()
            )->where('element_sets.record_type IS NULL OR element_sets.record_type = "Item"')
            ->order(array('element_sets.name', 'elements.name'));
        return $db->fetchAll($select);
    }

    /**
     * Get elements array used as select options.
     *
     * @return array
     */
    public function getElementsForSelect()
    {
        $options = array('' => 'Select Below');
        foreach ($this->findElements() as $element) {
            $optGroup = __($element['element_set_name']);
            $value = __($element['element_name']);
            $options[$optGroup][$element['element_id']] = $value;
        }
        return $options;
    }
}
