<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use seschustle\ExampleCommentsApiClient\Exception\ApiRequestException;
use seschustle\ExampleCommentsApiClient\Exception\InvalidApiResponseException;
use Throwable;

/**
 * Main comments API client class.
 */
class Client
{
    public const BASE_URI = 'https://89b6a81e-5e8f-4acf-a069-72d9e03e90d8.mock.pstmn.io';

    private $apiToken;

    /**
     * Class constructor.
     * 
     * @param string $apiToken API token.
     * @param ClientInterface|null $httpClient PSR-18 HTTP client. Will try to be discovered if null.
     * @param RequestFactoryInterface|null $requestFactory PSR-17 Request factory. Will try to be discovered if null.
     * @param StreamFactoryInterface|null $streamFactory PSR-17 Stream factory. Will try to be discovered if null.
     */
    public function __construct(
        string $apiToken,
        private ?ClientInterface $httpClient = null,
        private ?RequestFactoryInterface $requestFactory = null,
        private ?StreamFactoryInterface $streamFactory = null
        )
    {
        $this->apiToken = $apiToken;
        $this->httpClient ??= Psr18ClientDiscovery::find();
        $this->requestFactory ??= Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory ??= Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * Get all comments.
     *
     * @return Comment[]
     * 
     * @throws InvalidApiResponseException When comment DTO creation fails.
     */
    public function getAll(): array
    {
        $responseData = $this->makeRequest('GET', '/comments');
        
        $comments = [];
        foreach ($responseData as $comment) {
            try {
                $comments[] = Comment::fromArray($comment);
            } catch (Throwable $e) {
                throw new InvalidApiResponseException('Failed to create Comment DTO', 0, $e);
            }
        }
            
        return $comments;
    }

    /**
     * Create a new comment.
     *
     * @param array $data Comment data with 'name' and 'text' keys.
     * 
     * @return Comment Created comment.
     * 
     * @throws InvalidApiResponseException When comment DTO creation fails.
     */
    public function createComment(array $data): Comment
    {
        $responseData = $this->makeRequest('POST', '/comments', $data);

        try {
            return Comment::fromArray($responseData);
        } catch (Throwable $e) {
            throw new InvalidApiResponseException('Failed to create Comment DTO', 0, $e);
        }
    }

    /**
     * Update an existing comment.
     *
     * @param int $id Comment ID.
     * @param array $data Updated comment date. 
     * 
     * @return Comment Updated comment.
     * 
     * @throws InvalidApiResponseException When comment DTO creation fails.
     */
    public function updateComment(int $id, array $data): Comment
    {
        $responseData = $this->makeRequest('PUT', "/comments/$id", [
            'name' => $data['name'],
            'text' => $data['text'],
        ]);

        try {
            return Comment::fromArray($responseData);
        } catch (Throwable $e) {
            throw new InvalidApiResponseException('Failed to create Comment DTO', 0, $e);
        }
    }

    /**
     * Make HTTP request with optional JSON body and handle exceptions.
     *
     * @param string $method HTTP method (GET, POST, PUT, etc.).
     * @param string $path Request path (will be appended to BASE_URI).
     * @param array|null $options Request options (will be sent as JSON body).
     * 
     * @return array Decoded response data.
     * 
     * @throws ApiRequestException When HTTP request fails.
     * @throws InvalidApiResponseException When response is not valid JSON.
     */
    private function makeRequest(string $method, string $path, ?array $options = null): array
    {
        $request = $this
            ->requestFactory
            ->createRequest($method, self::BASE_URI . $path)
            ->withHeader('Authorization', 'Bearer ' . $this->apiToken)
            ->withHeader('Content-Type', 'application/json');
        
        if ($options !== null) {
            $body = $this->streamFactory->createStream(json_encode($options));
            $request = $request->withBody($body);
        }

        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new ApiRequestException('Failed to send HTTP request', 0, $e);
        }

        $responseData = json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidApiResponseException('Invalid JSON response');
        }

        return $responseData;
    }
}
