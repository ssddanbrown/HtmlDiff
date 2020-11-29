<?php namespace HtmlDiff;

class Diff
{

    /**
     * This value defines balance between speed and memory utilization.
     * The higher it is the faster it works and more memory consumes.
     * @var int
     */
    private $matchGranularityMaximum = 4;

    private $content = "";
    private $newText = "";
    private $oldText = "";

    private static $specialCaseClosingTags = [
        '</strong>' => 0,
        '</em>' => 0,
        '</b>' => 0,
        '</i>' => 0,
        '</big>' => 0,
        '</small>' => 0,
        '</u>' => 0,
        '</sub>' => 0,
        '</sup>' => 0,
        '</strike>' => 0,
        '</s>' => 0,
    ];

    private static $specialCaseOpeningTagRegex = '/<((strong)|(b)|(i)|(em)|(big)|(small)|(u)|(sub)|(sup)|(strike)|(s))[\>\s]+/i';

    /**
     * Tracks opening and closing formatting tags to ensure that we don't
     * inadvertently generate invalid html during the diff process.
     * @var string[]
     */
    private $specialTagDiffStack = [];

    private $newWords = [];
    private $oldWords = [];
    private $matchGranularity = 1;
    private $blockExpressions = [];

    /**
     * Defines how to compare repeating words. Valid values are from 0 to 1. This value allows to exclude
     * some words from comparison that eventually reduces the total time of the diff algorithm. 0 means
     * that all words are excluded so the diff will not find any matching words at all. 1 (default value)
     * means that all words participate in comparison so this is the most accurate case.
     * 0.5 means that any word that occurs more than 50% times may be excluded from comparison.
     * This doesn't mean that such words will definitely be excluded but only gives a permission
     * to exclude them if necessary.
     * @var double
     */
    public $repeatingWordsAccuracy = 1;

    /**
     * If true all whitespaces are considered as equal.
     * @var bool
     */
    public $ignoreWhitespaceDifferences = true;

    /**
     * If some match is too small and located far from its neighbors then it is considered as orphan
     * and removed. This property defines relative size of the match to be considered as orphan, from 0 to 1.
     * 1 means that all matches will be considered as orphans.
     * 0 (default) means that no match will be considered as orphan.
     * 0.2 means that if match length is less than 20% of distance between its neighbors it is considered as orphan.
     * @var double
     */
    public $orphanMatchThreshold = 0;


    public function __construct(string $oldText, string $newText)
    {
        $this->newText = $newText;
        $this->oldText = $oldText;
    }

    public static function exceute(string $oldText, string $newText): string
    {
        return (new static($oldText, $newText))->build();
    }

    /**
     * Builds the HTML diff output returning the diff HTML.
     */
    public function build(): string
    {
        if ($this->oldText === $this->newText) {
            return $this->newText;
        }

        // TODO
    }

    /**
     * Uses the given $expression to group text together so that any change
     * detected within the group is treated as a single block.
     */
    public function addBlockExpression(string $expression): void
    {
        $this->blockExpressions[] = $expression;
    }

    private function splitInputIntoWords(): void
    {
        $this->oldWords = WordSplitter::convertHtmlToListOfWords($this->oldText, $this->blockExpressions);
        // free memory, allow it for GC
        $this->oldText = "";

        $this->newWords = WordSplitter::convertHtmlToListOfWords($this->newText, $this->blockExpressions);
        // free memory, allow it for GC
        $this->newText = "";
    }

    private function performOperation(Operation $operation): void
    {
        // TODO
    }


    private function findMatchingBlocks(int $startInOld, int $endInOld, int $startInNew, int $endInNew, array &$matchingBlocks): void
    {
        $match = $this->findMatch($startInOld, $endInOld, $startInNew, $endInNew);

        if (!is_null($match)) {
            if ($startInOld < $match->startInOld && $startInNew < $match->startInNew) {
                $this->findMatchingBlocks($startInOld, $match->startInOld, $startInNew, $match->startInNew, $matchingBlocks);
            }

            $matchingBlocks[] = $match;

            if ($match->getEndInOld() < $endInOld && $match->getEndInNew() < $endInNew) {
                $this->findMatchingBlocks($match->getEndInOld(), $endInOld, $match->getEndInNew(), $endInNew, $matchingBlocks);
            }
        }
    }

    private function findMatch(int $startInOld, int $endInOld, int $startInNew, int $endInNew): ?Match
    {
        // For large texts it is more likely that there is a Match of size bigger than maximum granularity.
        // If not then go down and try to find it with smaller granularity.
        for ($i = $this->matchGranularity; $i > 0; $i--) {
            $options = new MatchOptions();
            $options->blockSize = $i;
            $options->repeatingWordsAccuracy = $this->repeatingWordsAccuracy;
            $options->ignoreWhitespaceDifferences = $this->ignoreWhitespaceDifferences;

            $finder = new MatchFinder($this->oldWords, $this->newWords, $startInOld, $endInOld, $startInNew, $endInNew, $options);
            $match = $finder->findMatch();
            if (!is_null($match)) {
                return $match;
            }
        }
        return null;
    }

}