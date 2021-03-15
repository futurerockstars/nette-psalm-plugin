<?php

namespace FutureRockstars\NettePsalmPlugin;

use SimpleXMLElement;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use PhpParser\Node;
use Psalm\Codebase;
use Psalm\FileSource;
use Psalm\Plugin\Hook\AfterClassLikeVisitInterface;
use Psalm\Storage\ClassLikeStorage;

class Plugin implements PluginEntryPointInterface, AfterClassLikeVisitInterface
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

    public static function afterClassLikeVisit(
        Node\Stmt\ClassLike $stmt,
        ClassLikeStorage $storage,
        FileSource $statements_source,
        Codebase $codebase,
        array &$file_replacements = []
    ) {
        if ($storage->user_defined
            && !$storage->is_interface
            && \class_exists($storage->name)
        ) {
            $reflection = new \ReflectionClass($storage->name);
            if($reflection->isSubclassOf(\Nette\Application\UI\Control::class)
               || $reflection->isSubclassOf(\Nette\Bridges\ApplicationLatte\Template::class)
            ) {
                $storage->suppressed_issues[] = 'PropertyNotSetInConstructor';
            }

        }
    }
}
