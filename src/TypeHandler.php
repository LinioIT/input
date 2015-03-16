<?php

namespace Linio\Component\Input;

use Linio\Component\Input\Transformer\TransformerInterface;
use Linio\Component\Input\Transformer\DateTimeTransformer;

class TypeHandler
{
    protected $typeChecks = [];
    protected $typeTransformers = [];

    /**
     * @param callable[] $typeChecks
     * @param TransformerInterface[] $typeTransformers
     */
    public function __construct(array $typeChecks = array(), array $typeTransformers = array())
    {
        foreach ($typeChecks as $typeName => $typeCheck) {
            $this->addTypeCheck($typeName, $typeCheck);
        }

        foreach ($typeTransformers as $typeName => $typeTransformer) {
            $this->addTypeTransformer($typeName, $typeTransformer);
        }

        $this->loadDefaults();
    }

    protected function loadDefaults()
    {
        $this->typeChecks = [
            'boolean' => 'is_bool',
            'float' => 'is_float',
            'double' => 'is_float',
            'int' => 'is_int',
            'integer' => 'is_int',
            'numeric' => 'is_numeric',
            'string' => 'is_string',
        ];

        $this->typeTransformers = [
            'datetime' => new DateTimeTransformer(),
        ];
    }

    /**
     * @param string $type
     * @param string $value
     */
    public function checkType($type, $value)
    {
        if (!isset($this->typeChecks[$type])) {
            return true;
        }

        return call_user_func($this->typeChecks[$type], $value);
    }

    /**
     * @param string $type
     * @param string $value
     */
    public function convertType($type, $value)
    {
        if (!isset($this->typeTransformers[$type])) {
            return $value;
        }

        return $this->typeTransformers[$type]->transform($value);
    }

    /**
     * @param string $typeName
     * @param string $typeCheck
     */
    public function addTypeCheck($typeName, callable $typeCheck)
    {
        $this->typeChecks[$typeName] = $typeCheck;
    }

    /**
     * @return array
     */
    public function getTypeChecks()
    {
        return $this->typeChecks;
    }

    /**
     * @param array $typeChecks
     */
    public function setTypeChecks(array $typeChecks)
    {
        $this->typeChecks = $typeChecks;
    }

    /**
     * @param string $typeName
     * @param \Linio\Component\Input\Transformer\TransformerInterface $typeTransformers
     */
    public function addTypeTransformer($typeName, TransformerInterface $typeTransformer)
    {
        $this->typeTransformers[$typeName] = $typeTransformer;
    }

    /**
     * @return array
     */
    public function getTypeTransformers()
    {
        return $this->typeTransformers;
    }

    /**
     * @param array $typeTransformers
     */
    public function setTypeTransformers(array $typeTransformers)
    {
        $this->typeTransformers = $typeTransformers;
    }
}
