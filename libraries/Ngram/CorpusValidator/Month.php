<?php
class Ngram_CorpusValidator_Month extends Ngram_CorpusValidator_AbstractCorpusValidator
{
    public function addItem($id, $text)
    {
        $text = trim($text);
        if (preg_match('/^\d{4}$/', $text)) {
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
