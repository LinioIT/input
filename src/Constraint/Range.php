<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Range implements ConstraintInterface
{
    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    public function __construct(int $min, int $max = PHP_INT_MAX)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function validate($content): bool
    {
        return $content >= $this->min && $content <= $this->max;
    }

    public function getErrorMessage(): string
    {
        return sprintf('Value is not between %d and %d', $this->min, $this->max);
    }
}
