<?php
class Table_NgramCorpus extends Omeka_Db_Table
{
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

    public function getElementsForSelect()
    {
        $options = array('' => __('Select Below'));
        foreach ($this->findElements() as $element) {
            $optGroup = __($element['element_set_name']);
            $value = __($element['element_name']);
            $options[$optGroup][$element['element_id']] = $value;
        }
        return $options;
    }
}
