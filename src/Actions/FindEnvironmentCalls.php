<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Illuminate\Support\Collection;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\Parser;
use Worksome\Envy\Contracts\Actions\FindsEnvironmentCalls;
use Worksome\Envy\Support\EnvironmentCall;
use Worksome\Envy\Support\PhpParser\EnvCallNodeVisitor;

use function Safe\file_get_contents;

final class FindEnvironmentCalls implements FindsEnvironmentCalls
{
    public function __construct(private Parser $parser)
    {
    }

    public function __invoke(string $filePath, bool $excludeVariablesWithDefaults = false): Collection
    {
        $traverser = new NodeTraverser();
        $envCallNodeVisitor = new EnvCallNodeVisitor($filePath, $excludeVariablesWithDefaults);

        $traverser->addVisitor(new NodeConnectingVisitor());
        $traverser->addVisitor($envCallNodeVisitor);
        $statements = $this->parser->parse(file_get_contents($filePath));

        if ($statements === null) {
            return $envCallNodeVisitor->getEnvironmentVariables();
        }

        $traverser->traverse($statements);

        return $envCallNodeVisitor
            ->getEnvironmentVariables()
            ->when(
                $excludeVariablesWithDefaults,
                fn (Collection $variables) => $variables->reject(
                    fn (EnvironmentCall $variable) => $variable->getDefault() !== null
                )
            );
    }
}
