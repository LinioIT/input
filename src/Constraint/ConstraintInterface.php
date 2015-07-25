<?php

namespace Linio\Component\Input\Constraint;

interface ConstraintInterface
{
    /**
     * @param string $content
     *
     * @return bool
     */
    public function validate($content);

    /**
     * @return string
     */
    public function getErrorMessage();
}
