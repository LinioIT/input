<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class NotNull implements ConstraintInterface
{
    public function validate($content): bool
    {
        if ($content) {
            $content = trim($content);
        }

        return $content !== null && $content !== '';
    }

    public function getErrorMessage(): string
    {
        return 'Unexpected empty content';
    }
}
