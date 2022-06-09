<?php

namespace Huboo\I18nLoader\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;

/**
 * Class TestCase
 *
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;
    use WithFaker;
    use WithoutEvents;
    use GetTranslations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setConfigCache();
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $headers
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function patch($uri, array $data = [], array $headers = [])
    {
        $this->withHeader('X-Requested-With', 'XMLHttpRequest');

        return parent::patch($uri, $data, $headers);
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $headers
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function patchJson($uri, array $data = [], array $headers = [])
    {
        $this->withHeader('X-Requested-With', 'XMLHttpRequest');

        return parent::patchJson($uri, $data, $headers);
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $headers
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        $this->withHeader('X-Requested-With', 'XMLHttpRequest');

        return parent::post($uri, $data, $headers);
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $headers
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function postJson($uri, array $data = [], array $headers = [])
    {
        $this->withHeader('X-Requested-With', 'XMLHttpRequest');

        return parent::postJson($uri, $data, $headers);
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $headers
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    public function getJson($uri, array $data = [])
    {
        $this->withHeader('X-Requested-With', 'XMLHttpRequest');

        return parent::getJson($uri, $data);
    }

}
