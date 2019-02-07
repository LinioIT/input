<?php

declare(strict_types=1);

namespace Linio\Component\Input;

use Linio\Component\Input\Node\BaseNode;

class SchemaBuilder
{
    public function build(InputHandler $inputHandler): array
    {
        $inputHandler->define();

        return $this->walk($inputHandler->getRoot());
    }

    protected function walk(BaseNode $node): array
    {
        if (!$node->hasChildren()) {
            return [];
        }

        foreach ($node->getChildren() as $field => $childNode) {
            $schema[$field]['type'] = $childNode->getTypeAlias();
            $schema[$field]['required'] = $childNode->isRequired();
            $schema[$field]['default'] = $childNode->getDefault();
            $schema[$field]['nullable'] = $childNode->allowNull();
            $schema[$field]['children'] = $this->walk($childNode);
        }

        return $schema;
    }
}
