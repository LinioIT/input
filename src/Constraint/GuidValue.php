<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class GuidValue extends Constraint
{
    const ERROR_MESSAGE = 'Invalid GUID format';

    public function __construct(string $errorMessage = null)
    {
        $this->setErrorMessage($errorMessage ?? static::ERROR_MESSAGE);
    }

    public function validate($content): bool
    {
        if (!is_string($content) || strlen($content) != 36) {
            return false;
        }

        return (bool) preg_match('/^[0-9a-fA-F]{8}\-([0-9a-fA-F]{4}\-){3}[0-9a-fA-F]{12}$/', $content);
    }
}
