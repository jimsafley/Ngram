<?php
interface Ngram_SequenceType_SequenceTypeInterface
{
    public function getLabel();

    public function setItems(array $items);

    public function getValidItems();

    public function getInalidItems();
}
