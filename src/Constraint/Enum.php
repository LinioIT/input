<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Enum implements ConstraintInterface
{
    /**
     * @var array
     */
    protected $enumValues = [];

    public function __construct(array $enumValues)
    {
        $this->enumValues = $enumValues;
    }

    public function validate($content): bool
    {
        return in_array($content, $this->enumValues);
    }

    public function getErrorMessage(): string
    {
        return 'Invalid option for enum. Allowed options are: ' . implode(', ', $this->enumValues);
    }
}
