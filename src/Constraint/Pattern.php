<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Pattern implements ConstraintInterface
{
    /**
     * @var string
     */
    protected $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    public function validate($content): bool
    {
        if (!$content) {
            return false;
        }

        return (bool) preg_match($this->pattern, $content);
    }

    public function getErrorMessage(): string
    {
        return 'Required pattern does not match';
    }
}
