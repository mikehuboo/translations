<?php

namespace Huboo\Translations\Jobs;

use Exception;
use Spatie\WebhookClient\ProcessWebhookJob as SpatieProcessWebhookJob;

class ClearTranslationCacheJob extends SpatieProcessWebhookJob
{
    /**
     * @throws Exception
     */
    public function handle()
    {
        app('translation.cache')->rebuildAllCaches();
    }
}
