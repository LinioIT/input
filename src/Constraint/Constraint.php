<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

abstract class Constraint implements ConstraintInterface
{
    /**
     * @var string
     */
    protected $errorMessage;

    public function getErrorMessage(string $field): string
    {
        return sprintf('[%s] %s', $field, $this->errorMessage);
    }

    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }
}
