<?php

namespace Bb\ConsentBanner\Utility;

use Countable;
use TYPO3\CMS\Core\Utility\MathUtility;

class Counter implements Countable
{
    /**
     * @var int $count The current count.
     */
    private int $count;
    /**
     * @var int $stepCount The step count.
     */
    private int $stepCount;
    /**
     * @var int $initialCount The initial count value.
     */
    private int $initialCount;
    /**
     * Constructor for the Counter class.
     *
     * @param int $initialCount The initial count value.
     */
    public function __construct(int $initialCount = 0, int $stepCount = 1) {
        $this->count =  $this->initialCount = $initialCount;
        $this->stepCount = $stepCount;
    }
    /**
     * Returns the item count of the object
     */
    public function count(): int
    {
        return $this->count;
    }
    public function increment(): void
    {
        $this->count+=$this->stepCount;
    }
    /**
     * Decrement the count by 1.
     */
    public function decrement(): void
    {
        $this->count-=$this->stepCount;
    }
    /**
     * Reset the count to the initial value.
     *
     * @param int|null $initialCount The initial count value.
     * @param int|null $stepCount The step count.
     */
    public function reset(?int $initialCount = null, ?int $stepCount = null): void {
        if(is_null($initialCount)){
            $this->count = $this->initialCount;
        }elseif(MathUtility::canBeInterpretedAsInteger($initialCount)){
            $this->count = $initialCount;
        }else{
            $this->count = 0;
        }

        if (!is_null($stepCount)) {
            $this->stepCount = $stepCount;
        }
    }
}