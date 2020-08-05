<?php

declare(strict_types=1);

namespace Linio\Component\Input\Node;

use Linio\Component\Input\Constraint\ConstraintInterface;
use Linio\Component\Input\Exception\InvalidConstraintException;
use Linio\Component\Input\Exception\RequiredFieldException;
use Linio\Component\Input\InputHandler;
use Linio\Component\Input\Instantiator\InstantiatorInterface;
use Linio\Component\Input\Transformer\TransformerInterface;
use Linio\Component\Input\TypeHandler;

class BaseNode
{
    /**
     * @var ConstraintInterface[]
     */
    protected $constraints = [];

    /**
     * @var TransformerInterface
     */
    protected $transformer;

    /**
     * @var InstantiatorInterface
     */
    protected $instantiator;

    /**
     * @var string
     */
    protected $type = 'array';

    /**
     * @var string
     */
    protected $typeAlias = 'array';

    /**
     * @var bool
     */
    protected $required = true;

    protected $default;

    /**
     * @var BaseNode[]
     */
    protected $children = [];

    /**
     * @var TypeHandler
     */
    protected $typeHandler;

    /**
     * @var bool
     */
    protected $allowNull = false;

    public function setConstraints(array $constraints): self
    {
        $this->constraints = $constraints;

        return $this;
    }

    public function addConstraint(ConstraintInterface $constraint): self
    {
        $this->constraints[] = $constraint;

        return $this;
    }

    public function addConstraints(array $constraints): self
    {
        $this->constraints = array_merge($this->constraints, $constraints);

        return $this;
    }

    public function setTransformer(TransformerInterface $transformer): self
    {
        $this->transformer = $transformer;

        return $this;
    }

    public function setInstantiator(InstantiatorInterface $instantiator): self
    {
        $this->instantiator = $instantiator;

        return $this;
    }

    public function setTypeHandler(TypeHandler $typeHandler): self
    {
        $this->typeHandler = $typeHandler;

        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setTypeAlias(string $typeAlias): self
    {
        $this->typeAlias = $typeAlias;

        return $this;
    }

    public function getTypeAlias(): string
    {
        return $this->typeAlias;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function setDefault($default): self
    {
        $this->default = $default;

        return $this;
    }

    public function setAllowNull(bool $allowNull): self
    {
        $this->allowNull = $allowNull;

        return $this;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function hasDefault(): bool
    {
        return (bool) $this->default;
    }

    public function add(string $key, string $type, array $options = [], InputHandler $handler = null): BaseNode
    {
        $child = $this->typeHandler->getType($type);

        if (isset($handler)) {
            $child = $child->setHandler($handler, $type);
        }

        if (isset($options['handler']) && !isset($handler)) {
            $child = $child->setHandler($options['handler'], $type);
        }

        if (isset($options['required'])) {
            $child->setRequired($options['required']);
        }

        if (isset($options['default'])) {
            $child->setDefault($options['default']);
        }

        if (isset($options['instantiator'])) {
            $child->setInstantiator($options['instantiator']);
        }

        if (isset($options['transformer'])) {
            $child->setTransformer($options['transformer']);
        }

        if (isset($options['constraints'])) {
            $child->addConstraints($options['constraints']);
        }

        if (isset($options['allow_null'])) {
            $child->setAllowNull($options['allow_null']);
        }

        $this->children[$key] = $child;

        return $child;
    }

    public function remove(string $key): void
    {
        unset($this->children[$key]);
    }

    /**
     * @return BaseNode[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    public function isRequired(): bool
    {
        if ($this->hasDefault()) {
            return false;
        }

        return $this->required;
    }

    public function allowNull(): bool
    {
        return $this->allowNull;
    }

    public function getValue(string $field, $value)
    {
        if ($this->allowNull() && $value === null) {
            return $value;
        }

        $this->checkConstraints($field, $value);

        if ($this->transformer) {
            return $this->transformer->transform($value);
        }

        return $value;
    }

    public function walk($input)
    {
        if (!is_array($input)) {
            return $input;
        }

        if (!$this->hasChildren()) {
            return $input;
        }

        $result = [];

        foreach ($this->getChildren() as $field => $config) {
            if (!array_key_exists($field, $input)) {
                if ($config->isRequired()) {
                    throw new RequiredFieldException($field);
                }

                if (!$config->hasDefault()) {
                    continue;
                }

                $input[$field] = $config->getDefault();
            }

            $result[$field] = $config->getValue($field, $config->walk($input[$field]));
        }

        return $result;
    }

    protected function checkConstraints(string $field, $value): void
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->validate($value) && ($this->isRequired() || $this->checkIfFieldValueIsSpecified($value))) {
                throw new InvalidConstraintException($constraint->getErrorMessage($field));
            }
        }
    }

    private function checkIfFieldValueIsSpecified($value): bool
    {
        return ($this->type === 'string' || $this->type === 'array' ? !empty($value) : !is_null($value));
    }

    private function setHandler(InputHandler $handler, string $type): self
    {
        $handler->setRootType($type);
        $handler->define();

        return $handler->getRoot();
    }
}
