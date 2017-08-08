<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Range extends Constraint
{
    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    public function __construct(int $min, int $max = PHP_INT_MAX, string $errorMessage = null)
    {
        $this->min = $min;
        $this->max = $max;

        $this->setErrorMessage($errorMessage ?? sprintf('Value is not between %d and %d', $this->min, $this->max));
    }

    public function validate($content): bool
    {
        if (!is_scalar($content)) {
            return false;
        }

        if ($content === null) {
            return false;
        }

        return $content >= $this->min && $content <= $this->max;
    }
}
