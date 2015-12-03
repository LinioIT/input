<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Type implements ConstraintInterface
{
    /**
     * @var string
     */
    protected $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function validate($content): bool
    {
        return call_user_func('is_' . $this->type, $content);
    }

    public function getErrorMessage(): string
    {
        return 'Value does not match type: ' . $this->type;
    }
}
