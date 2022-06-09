<?php

namespace Huboo\I18nLoader\Webhook\Validator;

use Illuminate\Http\Request;
use Spatie\WebhookClient\Exceptions\WebhookFailed;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;

class TranslationSignatureValidator implements SignatureValidator
{
    /**
     * @param Request $request
     * @param WebhookConfig $config
     *
     * @throws WebhookFailed
     *
     * @return bool
     */
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $signature = $request->header($config->signatureHeaderName);

        if (!$signature) {
            return false;
        }

        $signingSecret = $config->signingSecret;

        if (empty($signingSecret)) {
            throw WebhookFailed::signingSecretNotSet();
        }

        return hash_equals($signature, $signingSecret);
    }
}
