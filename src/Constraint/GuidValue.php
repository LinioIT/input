<?php

namespace Linio\Component\Input\Constraint;

class GuidValue implements ConstraintInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate($content)
    {
        if (strlen($content) != 36) {
            return false;
        }

        return (bool) preg_match('/^[0-9a-fA-F]{8}\-([0-9a-fA-F]{4}\-){3}[0-9a-fA-F]{12}$/', $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage()
    {
        return 'Invalid GUID format';
    }
}
