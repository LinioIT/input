<?php

declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Type extends Constraint
{
    /**
     * @var string
     */
    protected $type;

    public function __construct(string $type, string $errorMessage = null)
    {
        $this->type = $type;

        $this->setErrorMessage($errorMessage ?? 'Value does not match type: ' . $this->type);
    }

    public function validate($content): bool
    {
        return call_user_func('is_' . $this->type, $content);
    }
}
