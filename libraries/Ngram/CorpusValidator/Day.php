<?php
class Ngram_CorpusValidator_Day extends Ngram_CorpusValidator_CorpusValidatorType
{
    public function addItem($id, $text)
    {
        $text = trim($text);
        if (preg_match('/^\d{4}$/', $text)) {
            $this->invalidItems[] = $id;
        } elseif (preg_match('/^[a-z]+ \d+$/i', $text)) {
            $this->_invalidItems[] = $id;
        } elseif (preg_match('/^\d+-\d+$/i', $text)) {
            $this->_invalidItems[] = $id;
        } else {
            $timestamp = strtotime($text);
            if ($timestamp) {
                $this->_validItems[$id] = date('Ym', $timestamp);
            } else {
                $this->_invalidItems[] = $id;
            }
        }
    }
}
