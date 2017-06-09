<?php

declare(strict_types=1);

namespace Linio\Component\Input\Exception;

class RequiredFieldException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
