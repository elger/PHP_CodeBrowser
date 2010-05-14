<?php

class CbSourceIterator extends FilterIterator
{

    protected $knownExtensions = array('.php');

    public function __construct($sourceFolder)
    {
        parent::__construct(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sourceFolder),
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            )
        );
    }

    public function accept()
    {
        return in_array(
            strrchr($this->current(), '.'),
            $this->knownExtensions
        );

    }

    public function current()
    {
        return parent::current()->getRealPath();
    }


}