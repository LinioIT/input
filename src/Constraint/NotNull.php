<?php

namespace Linio\Component\Input\Constraint;

class NotNull implements ConstraintInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate($content)
    {
        $content = trim($content);

        return $content !== null && $content !== '';
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage()
    {
        return 'Unexpected empty content';
    }
}
