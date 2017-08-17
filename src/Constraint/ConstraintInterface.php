<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

interface ConstraintInterface
{
    public function validate($content): bool;

    public function getErrorMessage(string $field): string;
}
