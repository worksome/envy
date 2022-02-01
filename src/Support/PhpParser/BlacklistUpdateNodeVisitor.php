<?php

declare(strict_types=1);

namespace Worksome\Envy\Support\PhpParser;

use Illuminate\Support\Collection;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Worksome\Envy\Support\EnvironmentVariable;

final class BlacklistUpdateNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @param Collection<int, EnvironmentVariable> $updates
     */
    public function __construct(private Collection $updates)
    {
    }

    public function leaveNode(Node $node)
    {
        if (! $node instanceof Node\Expr\Array_) {
            return $node;
        }

        $blackListArrayItem = $node->getAttribute('parent');

        if (! $blackListArrayItem instanceof Node\Expr\ArrayItem) {
            return $node;
        }

        if (! $blackListArrayItem->key instanceof Node\Scalar\String_) {
            return $node;
        }

        if ($blackListArrayItem->key->value !== 'blacklist') {
            return $node;
        }

        $this->updates->each(function (EnvironmentVariable $variable) use (&$node) {
            $node->items[] = new Node\Expr\ArrayItem(new Node\Scalar\String_($variable->getKey()));
        });

        return $node;
    }
}
