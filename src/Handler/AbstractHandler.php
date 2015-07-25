<?php

namespace Linio\Component\Input\Handler;

use Doctrine\Common\Inflector\Inflector;
use Linio\Component\Input\TypeHandler;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractHandler
{
    /**
     * @var \Linio\Component\Input\TypeHandler
     */
    protected $typeHandler;

    /**
     * @var \Linio\Component\Input\Handler\FieldNode
     */
    protected $root;

    /**
     * @var array
     */
    protected $response = [];

    /**
     * @var array
     */
    protected $errors = [];

    public function __construct(TypeHandler $typeHandler)
    {
        $this->typeHandler = $typeHandler;
        $this->root = new FieldNode();
        $this->root->setType('array');
        $this->define();
    }

    /**
     * @return array
     */
    public function getData($index = null)
    {
        if (!$this->isValid()) {
            throw new \RuntimeException($this->getErrorsAsString());
        }

        if ($index) {
            return $this->response[$index];
        }

        return $this->response;
    }

    /**
     * @param string $key     Request parameter key
     * @param string $type    Request parameter type
     * @param array  $options
     */
    public function add($key, $type = 'string', $options = [])
    {
        return $this->root->add($key, $type, $options);
    }

    /**
     * @param string $type
     */
    public function remove($key)
    {
        $this->root->remove($key);
    }

    public function bind(Request $request)
    {
        $this->response = $this->walk($this->root, $request->request->all());
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getErrorsAsString()
    {
        return implode(', ', $this->errors);
    }

    protected function walk($node, $requestData, $parent = null)
    {
        $response = [];

        /*
         * TODO
         *
         * This loop needs some serious refactoring. Each node should have
         * it's own class (ArrayNode, ObjectNode, etc.) and handle data
         * properly by implementing methods from the NodeInterface.
         *
         * Only constraints, type checks and conversions should be handled in
         * this loop!
         */
        foreach ($node as $key => $item) {
            if (!isset($requestData[$key])) {
                if ($item->isRequired()) {
                    $this->errors[] = sprintf('"%s" is required', $key);
                }

                continue;
            }

            if ($item->isMixed()) {
                $response[$key] = $item;
            }

            if ($item->isCollection()) {
                $collectionType = $item->getCollectionType();

                foreach ($requestData[$key] as $requestItem) {
                    $instance = new $collectionType();
                    $this->walk($item, $requestItem, $instance);

                    if ($parent) {
                        $method = 'add' . Inflector::classify(Inflector::singularize($key));
                        $parent->$method($instance);
                    } else {
                        $response[$key][] = $instance;
                    }
                }

                continue;
            }

            if ($item->isScalarCollection()) {
                $scalarData = $requestData[$key];
                foreach ($scalarData as $value) {
                    if (!call_user_func('is_' . $item->getCollectionType(), $value)) {
                        $this->errors[] = sprintf('Value "%s", from "%s", is not of type %s', $value, $key, $item->getCollectionType());
                    }
                }

                if ($parent) {
                    $method = 'set' . Inflector::classify($key);
                    $parent->$method($scalarData);
                } else {
                    $response[$key] = $scalarData;
                }

                continue;
            }

            if ($item->hasChildren()) {
                $response[$key] = $this->walk($item, $requestData[$key]);
                continue;
            }

            if ($item->isObject()) {
                $class = $item->getType();
                $instance = new $class();
                $this->walk($item, $requestData[$key], $instance);

                if ($parent) {
                    $method = 'set' . Inflector::classify($key);
                    $parent->$method($instance);
                } else {
                    $response[$key] = $instance;
                }

                continue;
            }

            if (!$this->typeHandler->checkType($item->getType(), $requestData[$key])) {
                $this->errors[] = sprintf('"%s" should be of type %s, %s received.', $key, $item->getType(), gettype($requestData[$key]));
                continue;
            }

            foreach ($item->getConstraints() as $contraint) {
                if (!$contraint->validate($requestData[$key])) {
                    $this->errors[] = sprintf('"%s" constraint: %s', $key, $contraint->getErrorMessage());
                    continue;
                }
            }

            $value = $this->typeHandler->convertType($item->getType(), $requestData[$key]);

            if ($parent) {
                $method = 'set' . Inflector::classify($key);
                $parent->$method($value);
            } else {
                $response[$key] = $value;
            }
        }

        return $response;
    }

    /**
     * @return \Linio\Component\Input\TypeHandler
     */
    public function getTypeHandler()
    {
        return $this->typeHandler;
    }

    /**
     * @param \Linio\Component\Input\TypeHandler $typeHandler
     */
    public function setTypeHandler($typeHandler)
    {
        $this->typeHandler = $typeHandler;

        return $this;
    }
}
