<?php
class Ngram_CorpusValidator_Numeric extends Ngram_CorpusValidator_AbstractCorpusValidator
{
    public function addItem($id, $text)
    {
        $text = trim($text);
        if (is_numeric($text)) {
            $this->_validItems[$id] = $text;
        } else {
            $this->_invalidItems[] = $id;
        }
    }
}
