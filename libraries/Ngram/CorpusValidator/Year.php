<?php
class Ngram_CorpusValidator_Year extends Ngram_CorpusValidator_AbstractCorpusValidator
{
    public function addItem($id, $text)
    {
        $text = trim($text);
        if (preg_match('/^\d{4}$/', $text)) {
            $this->_validItems[$id] = $text;
        } else {
            $timestamp = strtotime($text);
            if ($timestamp) {
                $this->_validItems[$id] = date('Y', $timestamp);
            } else {
                $this->_invalidItems[] = $id;
            }
        }
    }
}
