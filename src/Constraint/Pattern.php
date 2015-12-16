<?php
declare(strict_types=1);

namespace Linio\Component\Input\Constraint;

class Pattern extends Constraint
{
    /**
     * @var string
     */
    protected $pattern;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
        $this->errorMessage = 'Required pattern does not match';
    }

    public function validate($content): bool
    {
        if (!$content) {
            return false;
        }

        return (bool) preg_match($this->pattern, $content);
    }
}
