<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class NativeEnum extends Constraint
{
    protected $enumClass;

    public function __construct($enumClass)
    {
        $this->enumClass = $enumClass;

        $this->setErrorMessage(
            $errorMessage ?? 'Invalid option for a native PHP enum. Allowed options are: ' . json_encode($this->enumClass::cases())
        );
    }

    public function validate($content): bool
    {
        if (!is_scalar($content)) {
            return false;
        }

        return !($this->enumClass::tryFrom($content) === null);
    }
}
