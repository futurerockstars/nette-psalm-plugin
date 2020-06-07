<?php

namespace PatrickKusebauch\NettePsalmPlugin;

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
        return glob(__DIR__ . '/stubs/*.phpstub') ?: [];
    }
}
