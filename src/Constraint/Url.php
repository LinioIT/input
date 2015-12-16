<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Url extends Constraint
{
    public function __construct()
    {
        $this->errorMessage = 'Invalid URL format';
    }

    public function validate($content): bool
    {
        return (bool) filter_var($content, FILTER_VALIDATE_URL);
    }
}
