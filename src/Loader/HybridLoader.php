<?php

declare(strict_types=1);

namespace Huboo\I18nLoader\Loader;

use Huboo\I18nLoader\I18nLoaderCache;
use Exception;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;

class HybridLoader extends FileLoader implements Loader
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The default path for the loader.
     *
     * @var string
     */
    protected $path;

    /**
     * All the registered paths to JSON translation files.
     *
     * @var array
     */
    protected $jsonPaths = [];

    /**
     * All the namespace hints.
     *
     * @var array
     */
    protected $hints = [];

    /**
     * @var I18nLoaderCache
     */
    private $cache;

    /**
     * Create a new hybrid loader instance.
     *
     * @param Filesystem $files
     * @param string $path
     * @param I18nLoaderCache $cache
     */
    public function __construct(Filesystem $files, string $path, I18nLoaderCache $cache)
    {
        parent::__construct($files, $path);

        $this->files = $files;
        $this->path = $path;
        $this->cache = $cache;
    }

    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string|null $namespace
     *
     * @throws Exception
     *
     * @return array|null
     */
    public function load($locale, $group, $namespace = null): ?array
    {
        $translations = $this->cache->getLanguage($locale);

        if ($group === '*' && $namespace === '*') {
            return $translations;
        }

        if ((is_null($namespace) || $namespace === '*') && isset($translations[$group])) {
            return $translations[$group];
        }

        if (isset($translations[$namespace]) && isset($translations[$namespace][$group])) {
            return $translations[$namespace][$group];
        }

        return $this->loadNamespaced($locale, $group, $namespace);
    }
}
