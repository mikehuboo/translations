<?php

namespace Huboo\I18nLoader\Jobs;

use Exception;
use Spatie\WebhookClient\ProcessWebhookJob as SpatieProcessWebhookJob;

class ClearI18nLoaderCacheJob extends SpatieProcessWebhookJob
{
    /**
     * @throws Exception
     */
    public function handle()
    {
        app('translation.cache')->rebuildAllCaches();
    }
}
