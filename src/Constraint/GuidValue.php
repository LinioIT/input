<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class GuidValue implements ConstraintInterface
{
    public function validate($content): bool
    {
        if (strlen($content) != 36) {
            return false;
        }

        return (bool) preg_match('/^[0-9a-fA-F]{8}\-([0-9a-fA-F]{4}\-){3}[0-9a-fA-F]{12}$/', $content);
    }

    public function getErrorMessage(): string
    {
        return 'Invalid GUID format';
    }
}
