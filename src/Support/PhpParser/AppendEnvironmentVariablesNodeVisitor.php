<?php

declare(strict_types=1);

namespace Worksome\Envy\Support\PhpParser;

use Illuminate\Support\Collection;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Worksome\Envy\Support\EnvironmentVariable;

final class AppendEnvironmentVariablesNodeVisitor extends NodeVisitorAbstract
{
    private bool $variablesWereAppended = false;

    /**
     * @param Collection<int, EnvironmentVariable> $updates
     */
    public function __construct(private Collection $updates, private string $arrayKey)
    {
    }

    public function leaveNode(Node $node)
    {
        if (! $node instanceof Node\Expr\Array_) {
            return $node;
        }

        $arrayItem = $node->getAttribute('parent');

        if (! $arrayItem instanceof Node\Expr\ArrayItem) {
            return $node;
        }

        if (! $arrayItem->key instanceof Node\Scalar\String_) {
            return $node;
        }

        if ($arrayItem->key->value !== $this->arrayKey) {
            return $node;
        }

        $this->updates->each(function (EnvironmentVariable $variable) use (&$node) {
            $node->items[] = new Node\Expr\ArrayItem(new Node\Scalar\String_($variable->getKey()));
        });

        $this->variablesWereAppended = true;

        return $node;
    }

    public function variablesWereAppended(): bool
    {
        return $this->variablesWereAppended;
    }
}
