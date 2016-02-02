<?php
abstract class Ngram_CorpusValidator_AbstractCorpusValidator
    implements Ngram_CorpusValidator_CorpusValidatorInterface
{
    protected $_validItems = array();

    protected $_invalidItems = array();

    public function getValidItems()
    {
        return $this->_validItems;
    }

    public function getInvalidItems()
    {
        return $this->_invalidItems;
    }
}
