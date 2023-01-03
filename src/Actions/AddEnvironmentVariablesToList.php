<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterAbstract;
use Worksome\Envy\Contracts\Actions\AddsEnvironmentVariablesToList;
use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\Exceptions\ConfigFileNotFoundException;
use Worksome\Envy\Support\PhpParser\AppendEnvironmentVariablesNodeVisitor;

use function Safe\file_get_contents;
use function Safe\file_put_contents;

final class AddEnvironmentVariablesToList implements AddsEnvironmentVariablesToList
{
    /**
     * The printer used to recreate the config file.
     */
    private PrettyPrinterAbstract $printer;

    public function __construct(
        private Parser $parser,
        private Finder $finder,
    ) {
        $this->printer = new Standard();
    }

    public function __invoke(Collection $updates, string $listKey): void
    {
        if ($this->finder->envyConfigFile() === null) {
            throw new ConfigFileNotFoundException();
        }

        $statements = $this->parser->parse(file_get_contents($this->finder->envyConfigFile()));

        if ($statements === null) {
            return;
        }

        $visitor = new AppendEnvironmentVariablesNodeVisitor($updates, $listKey);
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NodeConnectingVisitor());
        $traverser->addVisitor($visitor);
        $ast = $traverser->traverse($statements);

        if (! $visitor->variablesWereAppended()) {
            throw new InvalidArgumentException("[$listKey] is not a supported key in the envy.php config file.");
        }

        file_put_contents($this->finder->envyConfigFile(), $this->printer->prettyPrintFile($ast));
    }
}
