<?php
class Ngram_SequenceType_Year extends Ngram_SequenceType_AbstractSequenceType
{
    public function getLabel()
    {
        return 'Date by year';
    }

    public function setItems(array $items)
    {
        $validItems = array();
        $invalidItems = array();
        foreach ($items as $item) {
            $text = trim($item['text']);
            if (preg_match('/^\d{4}$/', $text)) {
                $validItems[] = array(
                    'id' => $item['id'],
                    'sequence_text' => $text,
                    'sequence_member' => $text,
                );
            } else {
                $timestamp = strtotime($text);
                if ($timestamp) {
                    $validItems[] = array(
                        'id' => $item['id'],
                        'sequence_text' => $text,
                        'sequence_member' => date('Y', $timestamp),
                    );
                } else {
                    $invalidItems[] = array(
                        'id' => $item['id'],
                        'sequence_text' => $text,
                    );
                }
            }
        }
        $this->validItems = $validItems;
        $this->invalidItems = $invalidItems;
    }
}
