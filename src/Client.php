<?php

declare(strict_types=1);

namespace Xvilo\GoFile;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use Http\Client\Common\Plugin\RedirectPlugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Xvilo\GoFile\Api\AccountApi;
use Xvilo\GoFile\HttpClient\HttpClientBuilder;

class Client
{
    /** @var string */
    final public const string VERSION = '0.1.0';

    public readonly AccountApi $account;

    public function __construct(
        private readonly HttpClientBuilder $httpClientBuilder = new HttpClientBuilder(),
        private readonly string $baseHost = 'https://api.gofile.io',
        private ?string $userAgent = null,
    ) {
        $this->setupHttpBuilder();
        $this->account = new AccountApi($this);
    }

    private function setupHttpBuilder(): void
    {
        $uri = Psr17FactoryDiscovery::findUriFactory()->createUri($this->baseHost);

        $this->httpClientBuilder
            ->addPlugin(new RedirectPlugin())
            ->addPlugin(new AddHostPlugin($uri))
            ->addPlugin(new HeaderAppendPlugin([
                'Accept' => 'application/json',
                'User-Agent' => $this->getUserAgent(),
        ]));
    }

    private function getUserAgent(): string
    {
        if ($this->userAgent === null) {
            $this->userAgent = sprintf(
                'GoFilePHP/%s PHP/%s %s/%s',
                self::VERSION,
                PHP_VERSION,
                php_uname('s'),
                php_uname('r'),
            );
        }

        return $this->userAgent;
    }

    public function getHttpClient(): HttpMethodsClient
    {
        return $this->getHttpClientBuilder()->getHttpClient();
    }

    protected function getHttpClientBuilder(): HttpClientBuilder
    {
        return $this->httpClientBuilder;
    }
}