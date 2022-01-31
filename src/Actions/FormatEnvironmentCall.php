<?php

declare(strict_types=1);

namespace Worksome\Envy\Actions;

use Illuminate\Support\Str;
use Worksome\Envy\Contracts\Actions\FormatsEnvironmentCall;
use Worksome\Envy\Support\EnvironmentCall;

use function Safe\preg_replace;

final class FormatEnvironmentCall implements FormatsEnvironmentCall
{
    public function __construct(
        private bool $displayComment = false,
        private bool $displayLocationHint = false,
        private bool $displayDefaultValue = false,
    ) {
    }

    public function __invoke(EnvironmentCall $environmentCall): string
    {
        $value = Str::of("{$environmentCall->getKey()}=");

        if ($this->displayDefaultValue && $environmentCall->getDefault() !== null) {
            $defaultValue = $environmentCall->getDefault();

            if (Str::match('/\s/', $defaultValue) !== '') {
                $defaultValue = "\"{$defaultValue}\"";
            }

            $value = $value->append($defaultValue);
        }

        if ($this->displayLocationHint) {
            $value = $value->start("# See {$environmentCall->getFile()}::{$environmentCall->getLine()}" . PHP_EOL);
        }

        if ($this->displayComment && $environmentCall->getComment() !== null) {
            $value = $value->start($this->formatComment($environmentCall->getComment()));
        }

        return $value->__toString();
    }

    private function formatComment(string $phpComment): string
    {
        // Remove PHP docblock syntax, such as '//' and '/*'
        $commentWithoutPhpSyntax = preg_replace('/^[\/*]+|\/\//m', '', $phpComment);

        // @phpstan-ignore-next-line
        return collect(explode(PHP_EOL, $commentWithoutPhpSyntax))
            ->map(fn (string $line) => "#{$line}")
            ->join(PHP_EOL) . PHP_EOL;
    }
}
