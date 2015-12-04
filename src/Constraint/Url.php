<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Url implements ConstraintInterface
{
    public function validate($content): bool
    {
        return (bool) filter_var($content, FILTER_VALIDATE_URL);
    }

    public function getErrorMessage(): string
    {
        return 'Invalid URL format';
    }
}
