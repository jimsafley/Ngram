<?php
class Ngram_SequenceType_Alphanumeric extends Ngram_SequenceType_AbstractSequenceType
{
    const MAX_LENGTH = 20;

    public function getLabel()
    {
        return 'Alphanumeric';
    }

    public function setItems(array $items)
    {
        $validItems = array();
        $invalidItems = array();
        foreach ($items as $item) {
            $text = trim($item['text']);
            if (self::MAX_LENGTH < strlen($text)) {
                $invalidItems[] = array(
                    'id' => $item['id'],
                    'sequence_text' => $text,
                );
            } else {
                $validItems[] = array(
                    'id' => $item['id'],
                    'sequence_text' => $text,
                    'sequence_member' => $text,
                );
            }
        }
        $this->validItems = $validItems;
        $this->invalidItems = $invalidItems;
    }
}
