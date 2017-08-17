<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Email extends Constraint
{
    public function __construct(string $errorMessage = null)
    {
        $this->setErrorMessage($errorMessage ?? 'Invalid email format');
    }

    public function validate($content): bool
    {
        return (bool) filter_var($content, FILTER_VALIDATE_EMAIL);
    }
}
