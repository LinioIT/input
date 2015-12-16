<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Enum extends Constraint
{
    /**
     * @var array
     */
    protected $enumValues = [];

    public function __construct(array $enumValues)
    {
        $this->enumValues = $enumValues;
        $this->errorMessage = 'Invalid option for enum. Allowed options are: ' . implode(', ', $this->enumValues);
    }

    public function validate($content): bool
    {
        return in_array($content, $this->enumValues);
    }
}
