<?php

namespace Linio\Component\Input;

use Doctrine\Common\Inflector\Inflector;

class Factory
{
    /**
     * @var string
     */
    protected $handlerNamespace;

    /**
     * @var \Linio\Component\Input\TypeHandler
     */
    protected $typeHandler;

    public function getHandler($alias)
    {
        $className = Inflector::classify($alias);
        $handlerClass = sprintf('\%s\%sHandler', $this->handlerNamespace, $className);

        if (!class_exists($handlerClass)) {
            throw new \RuntimeException('The specified handler class does not exist: ' . $handlerClass);
        }

        return new $handlerClass($this->typeHandler);
    }

    /**
     * @return string
     */
    public function getHandlerNamespace()
    {
        return $this->handlerNamespace;
    }

    /**
     * @param string $handlerNamespace
     */
    public function setHandlerNamespace($handlerNamespace)
    {
        $this->handlerNamespace = $handlerNamespace;

        return $this;
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
