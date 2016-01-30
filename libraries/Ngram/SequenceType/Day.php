<?php
class Ngram_SequenceType_Day extends Ngram_SequenceType_AbstractSequenceType
{
    public function getLabel()
    {
        return 'Date by day';
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
            } elseif (preg_match('/^[a-z]+ \d+$/i', $text)) {
                $invalidItems[] = array(
                    'id' => $item['id'],
                    'sequence_text' => $text,
                );
            } elseif (preg_match('/^\d+-\d+$/i', $text)) {
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
                        'sequence_member' => date('Ymd', $timestamp),
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
