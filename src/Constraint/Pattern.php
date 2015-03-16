<?php

namespace Linio\Component\Input\Constraint;

class Pattern implements ConstraintInterface
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @param string $pattern
     */
    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($content)
    {
        return (bool) preg_match($this->pattern, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage()
    {
        return 'Required pattern does not match';
    }
}
