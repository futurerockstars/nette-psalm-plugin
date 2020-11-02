<?php

namespace FutureRockstars\NettePsalmPlugin;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;

class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $psalm, ?SimpleXMLElement $config = null): void
    {
        foreach ($this->getStubFiles() as $file) {
            $psalm->addStubFile($file);
        }
    }
    /** @return array<string> */
    private function getStubFiles(): array
    {
        $phpstan = self::rglob(__DIR__ . '/../../phpstan/phpstan-nette/stubs/*/*.stub') ?: [];
        $mine = glob(__DIR__.'/stubs/*.phpstub') ?: [];
        return $phpstan + $mine;
    }

    private static function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, self::rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }

}
