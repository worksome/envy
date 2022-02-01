<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Illuminate\Support\Collection;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NodeConnectingVisitor;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterAbstract;
use Worksome\Envy\Contracts\Actions\UpdatesBlacklist;
use Worksome\Envy\Contracts\Finder;
use Worksome\Envy\Exceptions\ConfigFileNotFoundException;
use Worksome\Envy\Support\PhpParser\BlacklistUpdateNodeVisitor;

use function Safe\file_put_contents;
use function Safe\file_get_contents;

final class UpdateBlacklist implements UpdatesBlacklist
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

    public function __invoke(Collection $updates): void
    {
        if ($this->finder->envyConfigFile() === null) {
            throw new ConfigFileNotFoundException();
        }

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NodeConnectingVisitor());
        $traverser->addVisitor(new BlacklistUpdateNodeVisitor($updates));
        $statements = $this->parser->parse(file_get_contents($this->finder->envyConfigFile()));

        if ($statements === null) {
            return;
        }

        $ast = $traverser->traverse($statements);

        file_put_contents($this->finder->envyConfigFile(), $this->printer->prettyPrintFile($ast));
    }
}
