<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class GuidValue extends Constraint
{
    public function __construct()
    {
        $this->errorMessage = 'Invalid GUID format';
    }

    public function validate($content): bool
    {
        if (strlen($content) != 36) {
            return false;
        }

        return (bool) preg_match('/^[0-9a-fA-F]{8}\-([0-9a-fA-F]{4}\-){3}[0-9a-fA-F]{12}$/', $content);
    }
}
