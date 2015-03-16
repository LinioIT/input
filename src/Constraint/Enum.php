<?php

namespace Linio\Component\Input\Constraint;

class Enum implements ConstraintInterface
{
    /**
     * @var array
     */
    protected $enumValues;

    /**
     * @param array $enumValues
     */
    public function __construct(array $enumValues)
    {
        $this->enumValues = $enumValues;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($content)
    {
        return in_array($content, $this->enumValues);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorMessage()
    {
        return 'Invalid option for enum. Allowed options are: ' . implode(', ', $this->enumValues);
    }
}
