<?php
/**
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace PortlandLabs\CommunityBadgesClient;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Logging\LoggerFactory;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Exception;
use PortlandLabs\CommunityBadgesClient\Exceptions\CommunicatorError;
use PortlandLabs\CommunityBadgesClient\Exceptions\InvalidConfiguration;
use Psr\Log\LoggerInterface;

class ApiClient
{
    protected $config;
    protected $loggerFactory;
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(
        Repository $config,
        LoggerFactory $loggerFactory
    )
    {
        $this->config = $config;
        $this->loggerFactory = $loggerFactory;
        $this->logger = $this->loggerFactory->createLogger("community_api");
    }

    public function getClientId(): string
    {
        return $this->config->get('community_api_client.client_id', '');
    }

    public function setClientId(
        string $clientId
    ): self
    {
        $this->config->save('community_api_client.client_id', $clientId);

        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->config->get('community_api_client.client_secret', '');
    }

    public function setClientSecret(
        string $clientSecret
    ): self
    {
        $this->config->save('community_api_client.client_secret', $clientSecret);

        return $this;
    }

    public function getEndpoint(): string
    {
        return $this->config->get('community_api_client.endpoint', '');
    }

    public function setEndpoint(
        string $endpoint
    ): self
    {
        $this->config->save('community_api_client.endpoint', $endpoint);

        return $this;
    }

    private function hasValidConfiguration()
    {
        return strlen($this->getEndpoint()) > 0 &&
            strlen($this->getClientId()) > 0 &&
            strlen($this->getClientSecret()) > 0;
    }

    /**
     * @return Client
     */
    private function getClient(): ?Client
    {
        if ($this->hasValidConfiguration()) {
            $stack = HandlerStack::create();

            $stack->push(
                new OAuth2Middleware(
                    new ClientCredentials(
                        new Client([
                            'base_uri' => $this->getBaseUrl()->withPath('/oauth/2.0/token')
                        ]),
                        [
                            "client_id" => $this->getClientId(),
                            "client_secret" => $this->getClientSecret()
                        ]
                    )
                )
            );

            return new Client([
                'handler' => $stack,
                'auth' => 'oauth',
            ]);
        } else {
            return null;
        }
    }

    private function getBaseUrl(): Uri
    {
        return new Uri($this->getEndpoint());
    }

    /**
     * @param string $path
     * @param array $payload
     * @return array
     * @throws InvalidConfiguration
     * @throws CommunicatorError
     */
    public function doRequest(
        string $path,
        array $payload = [],
        string $method = "POST"
    ): array
    {
        $method = strtoupper($method);
        if ($this->hasValidConfiguration()) {
            $requestBody = json_encode($payload);
            if ($method === 'GET') {
                $requestBody = null;
            }
            /** @noinspection PhpComposerExtensionStubsInspection */
            $request = new Request(
                $method,
                $this->getBaseUrl()->withPath($path),
                [
                    "Content-Type" => "application/json"
                ],
                $requestBody
            );

            try {
                $response = $this->getClient()->send($request);

                $rawResponse = $response->getBody()->getContents();

                /** @noinspection PhpComposerExtensionStubsInspection */
                $jsonResponse = @json_decode($rawResponse, true);

                return $jsonResponse;

            } catch (GuzzleException $e) {
                // log the original error
                $this->logger->error($e->getMessage());
                throw new CommunicatorError($e->getMessage());
            } catch (Exception $e) {
                // log the original error
                $this->logger->error($e->getMessage());
                throw new CommunicatorError($e->getMessage());
            }
        }
    }
}