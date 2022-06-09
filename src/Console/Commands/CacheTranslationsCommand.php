<?php

namespace Huboo\I18nLoader\Console\Commands;

use Exception;
use Huboo\I18nLoader\I18nLoaderCache;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

/**
 * Class CacheTranslationsCommand
 */
class CacheTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuilds the redis cache for the backend translations';

    /**
     * @var I18nLoaderCache
     */
    protected $translationCache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param I18nLoaderCache $translationCache
     * @param LoggerInterface $logger
     */
    public function __construct(I18nLoaderCache $translationCache, LoggerInterface $logger)
    {
        parent::__construct();
        $this->translationCache = $translationCache;
        $this->logger = $logger;
    }

    /**
     * Execute the console command.
     *
     * @throws Exception
     *
     * @return mixed (0 on success, -1 on failure)
     */
    public function handle()
    {
        try {
            $this->translationCache->rebuildAllCaches();
            $this->info('Successfully rebuilt the translations cache.');
        } catch (Exception $exception) {
            $this->error('Unable to clear the translation cache: ' . $exception->getMessage());
            $this->logger->error('Unable to clear the translation cache', ['exception' => $exception]);
            throw $exception;
        }
    }
}
