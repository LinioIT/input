<?php
declare(strict_types=1);

namespace Linio\Component\Input;

use Linio\Component\Input\Node\BaseNode;

abstract class InputHandler
{
    /**
     * @var BaseNode
     */
    protected $root;

    /**
     * @var TypeHandler
     */
    protected $typeHandler;

    /**
     * @var array
     */
    protected $output = [];

    /**
     * @var array
     */
    protected $errors = [];

    public function __construct(TypeHandler $typeHandler = null)
    {
        $this->root = new BaseNode();
        $this->typeHandler = $typeHandler ?? new TypeHandler();
        $this->root->setTypeHandler($this->typeHandler);
    }

    public function add(string $key, string $type, array $options = []): BaseNode
    {
        return $this->root->add($key, $type, $options);
    }

    public function remove(string $key)
    {
        $this->root->remove($key);
    }

    public function bind(array $input)
    {
        $this->define();

        try {
            $this->output = $this->walk($this->root, $input);
        } catch (\RuntimeException $exception) {
            $this->errors[] = $exception->getMessage();
        }
    }

    public function getData($index = null)
    {
        if (!$this->isValid()) {
            throw new \RuntimeException($this->getErrorsAsString());
        }

        if ($index) {
            return $this->output[$index];
        }

        return $this->output;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorsAsString(): string
    {
        return implode(', ', $this->errors);
    }

    protected function walk(BaseNode $node, $input)
    {
        $result = [];

        if (!$node->hasChildren()) {
            return $input;
        }

        foreach ($node->getChildren() as $field => $config) {
            if (!array_key_exists($field, $input)) {
                if ($config->isRequired()) {
                    throw new \RuntimeException('Missing required field: ' . $field);
                }

                if (!$config->hasDefault()) {
                    continue;
                }

                $input[$field] = $config->getDefault();
            }

            $result[$field] = $config->getValue($field, $this->walk($config, $input[$field]));
        }

        return $result;
    }

    abstract public function define();
}
