<?php

namespace Tests\Feature\Webhook;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpFoundation\Response;

class WebhookTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->headerName = Config::get('webhook-client.configs.0.signature_header_name');
        $this->headerValue = Config::get('webhook-client.configs.0.signing_secret');
    }

    /** @test */
    public function failsValidationWithoutHeader()
    {
        $this->post('api/translations/update', [], [])
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /** @test */
    public function failsValidationWithoutSignature()
    {
        $this->post('api/translations/update', [], [$this->headerName => ''])
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /** @test */
    public function failsValidationWithoutSecretSetInConfig()
    {
        Config::set('webhooks.configs.0.signing_secret', '');

        $this->post('api/translations/update', [], [$this->headerName => ''])
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /** @test */
    public function passesValidation()
    {
        $this->post('api/translations/update', [], [$this->headerName => $this->headerValue])
          ->assertJson(['message' => 'ok'])
          ->assertSuccessful();
    }
}
