<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class DateRange implements ConstraintInterface
{
    /**
     * @var string
     */
    protected $min;

    /**
     * @var string
     */
    protected $max;

    public function __construct(string $min, string $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function validate($content): bool
    {
        $date = new \DateTime($content);

        return $date >= new \DateTime($this->min) && $date <= new \DateTime($this->max);
    }

    public function getErrorMessage(): string
    {
        return sprintf('Date is not between "%s" and "%s"', $this->min, $this->max);
    }
}
