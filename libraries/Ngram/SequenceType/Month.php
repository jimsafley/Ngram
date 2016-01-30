<?php
class Ngram_SequenceType_Month extends Ngram_SequenceType_AbstractSequenceType
{
    public function getLabel()
    {
        return 'Date by month';
    }

    public function setItems(array $items)
    {
        $validItems = array();
        $invalidItems = array();
        foreach ($items as $item) {
            $text = trim($item['text']);
            if (preg_match('/^\d{4}$/', $text)) {
                $invalidItems[] = array(
                    'id' => $item['id'],
                    'sequence_text' => $text,
                );
            } else {
                $timestamp = strtotime($text);
                if ($timestamp) {
                    $validItems[] = array(
                        'id' => $item['id'],
                        'sequence_text' => $text,
                        'sequence_member' => date('Ym', $timestamp),
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
