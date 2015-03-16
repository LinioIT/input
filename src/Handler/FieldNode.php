<?php

namespace Linio\Component\Input\Handler;

class FieldNode extends \ArrayObject
{
    const TYPE_STRING = 'string';
    const TYPE_COLLECTION = 'collection';
    const TYPE_ARRAY = 'array';
    const TYPE_MIXED = 'mixed';

    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var boolean
     */
    protected $required = true;

    /**
     * @var array
     */
    protected $constraints = [];

    /**
     * @param string $key Input field key
     * @param string $type Input field type
     * @param array $options
     */
    public function add($key, $type = self::TYPE_STRING, $options = [])
    {
        $node = new FieldNode();
        $node->setType($type);

        if (isset($options['required'])) {
            $node->setRequired($options['required']);
        }

        if (isset($options['constraints'])) {
            $node->setConstraints($options['constraints']);
        }

        $this->offsetSet($key, $node);

        return $node;
    }

    /**
     * @param string $type
     */
    public function remove($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * @return boolean
     */
    public function hasChildren()
    {
        return ($this->getType() == self::TYPE_ARRAY);
    }

    /**
     * @return boolean
     */
    public function isObject()
    {
        // Ugly hack due to class_exists() case-insensitiveness
        if ($this->type == 'datetime') {
            return false;
        }

        return (class_exists($this->type));
    }

    /**
     * @return boolean
     */
    public function isMixed()
    {
        return ($this->type == self::TYPE_MIXED);
    }

    /**
     * @return boolean
     */
    public function isCollection()
    {
        $collectionType = $this->getCollectionType();

        if (!class_exists($collectionType)) {
            return false;
        }

        return true;
    }

    /**
     * @return boolean
     */
    public function isScalarCollection()
    {
        $collectionType = $this->getCollectionType();

        if (!function_exists('is_' . $collectionType)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getCollectionType()
    {
        $pos = strrpos($this->type, '[]');

        if ($pos === false) {
            return false;
        }

        return substr($this->type, 0, $pos);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param String $type
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @param array $constraints
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
    }
}
