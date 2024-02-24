<?php

declare(strict_types=1);

namespace Xvilo\GoFile\HttpClient;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClientFactory;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class HttpClientBuilder
{
    private readonly ClientInterface $httpClient;

    private readonly RequestFactoryInterface $requestFactory;

    private readonly StreamFactoryInterface $streamFactory;

    /** @var Plugin[] */
    private array $plugins = [];

    private ?HttpMethodsClient $pluginClient = null;

    public function __construct(
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        $this->httpClient = $httpClient ?: Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory = $streamFactory ?: Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * Add a new plugin to the end of the plugin chain.
     */
    public function addPlugin(Plugin $plugin): self
    {
        $this->plugins[] = $plugin;
        $this->pluginClient = null;

        return $this;
    }

    /**
     * Remove a plugin by its fully qualified class name (FQCN).
     *
     * @param class-string<Plugin> $fqcn
     */
    public function removePlugin(string $fqcn): self
    {
        foreach ($this->plugins as $idx => $plugin) {
            if ($plugin instanceof $fqcn) {
                unset($this->plugins[$idx]);
                $this->pluginClient = null;
            }
        }

        return $this;
    }

    public function getHttpClient(): HttpMethodsClient
    {
        if (!$this->pluginClient instanceof HttpMethodsClient) {
            $plugins = $this->plugins;
            $this->pluginClient = new HttpMethodsClient(
                (new PluginClientFactory())->createClient($this->httpClient, $plugins),
                $this->requestFactory,
                $this->streamFactory
            );
        }

        return $this->pluginClient;
    }
}