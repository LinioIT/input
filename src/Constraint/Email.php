<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Email implements ConstraintInterface
{
    public function validate($content): bool
    {
        return (bool) filter_var($content, FILTER_VALIDATE_EMAIL);
    }

    public function getErrorMessage(): string
    {
        return 'Invalid email format';
    }
}
