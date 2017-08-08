<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class NotNull extends Constraint
{
    public function __construct(string $errorMessage = null)
    {
        $this->setErrorMessage($errorMessage ?? 'Unexpected empty content');
    }

    public function validate($content): bool
    {
        if ($content && is_string($content)) {
            $content = trim($content);
        }

        return $content !== null && $content !== '';
    }
}
