<?php namespace Ssddanbrown\HtmlDiff;

class MatchOptions
{
    /**
     * @var int Match granularity, defines how many words are joined into single block
     */
    public $blockSize;

    /**
     * @var float
     */
    public $repeatingWordsAccuracy;

    /**
     * @var bool
     */
    public $ignoreWhitespaceDifferences;
}