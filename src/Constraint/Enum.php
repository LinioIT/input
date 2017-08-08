<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Enum extends Constraint
{
    /**
     * @var array
     */
    protected $enumValues = [];

    /**
     * @var bool
     */
    protected $strictType;

    public function __construct(array $enumValues, string $errorMessage = null, $strictType = false)
    {
        $this->enumValues = $enumValues;
        $this->strictType = $strictType;

        $this->setErrorMessage(
            $errorMessage ?? 'Invalid option for enum. Allowed options are: ' . implode(', ', $this->enumValues)
        );
    }

    public function validate($content): bool
    {
        if (!is_scalar($content)) {
            return false;
        }

        return in_array($content, $this->enumValues, $this->strictType);
    }
}
