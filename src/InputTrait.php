<?php

namespace Linio\Component\Input;

trait InputTrait
{
    /**
     * @var \Linio\Component\Input\Factory
     */
    protected $inputFactory;

    /**
     * @param string $alias
     *
     * @return \Linio\Component\Input\Handler\AbstractHandler
     */
    protected function getInputHandler($alias)
    {
        return $this->inputFactory->getHandler($alias);
    }

    /**
     * @return \Linio\Component\Input\Factory
     */
    public function getInputFactory()
    {
        return $this->inputFactory;
    }

    /**
     * @param \Linio\Component\Input\Factory $inputFactory
     */
    public function setInputFactory($inputFactory)
    {
        $this->inputFactory = $inputFactory;

        return $this;
    }
}
