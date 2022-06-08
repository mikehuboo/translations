<?php

namespace Huboo\Translations\Console\Commands;

use Exception;
use Huboo\Translations\TranslationCache;
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
     * @var TranslationCache
     */
    protected $translationCache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param TranslationCache $translationCache
     * @param LoggerInterface $logger
     */
    public function __construct(TranslationCache $translationCache, LoggerInterface $logger)
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
