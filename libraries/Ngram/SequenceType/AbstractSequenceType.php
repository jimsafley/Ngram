<?php
abstract class Ngram_SequenceType_AbstractSequenceType
    implements Ngram_SequenceType_SequenceTypeInterface
{
    protected $validItems;

    protected $invalidItems;

    public function getValidItems()
    {
        return $this->validItems;
    }

    public function getInalidItems()
    {
        return $this->invalidItems;
    }
}
