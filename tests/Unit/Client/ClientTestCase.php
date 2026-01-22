<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient\Tests\Unit\Client;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use seschustle\ExampleCommentsApiClient\Client;

/**
 * Base test case for Client tests with common setup and helper methods.
 */
abstract class ClientTestCase extends TestCase
{
    protected ClientInterface $httpClient;
    protected RequestFactoryInterface $requestFactory;
    protected StreamFactoryInterface $streamFactory;
    protected Client $client;

    /**
     * Basic setup for test cases.
     * 
     * @return void
     */
    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(ClientInterface::class);
        $this->requestFactory = $this->createMock(RequestFactoryInterface::class);
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);

        $this->requestFactory
            ->method('createRequest')
            ->willReturnCallback(fn(string $method, string $uri) => $this->createMockRequest($method, $uri));

        $this->streamFactory
            ->method('createStream')
            ->willReturnCallback(fn(string $content) => $this->createMockStream($content));

        $this->client = new Client(
            'test-api-token',
            $this->httpClient,
            $this->requestFactory,
            $this->streamFactory
        );
    }

    /**
     * Mock request with given method and URI.
     * 
     * @param string $method
     * @param string $uri
     * 
     * @return RequestInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createMockRequest(string $method, string $uri): RequestInterface
    {
        $uriMock = $this->createMock('Psr\Http\Message\UriInterface');
        $uriMock->method('__toString')->willReturn($uri);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn($method);
        $request->method('getUri')->willReturn($uriMock);
        $request->method('hasHeader')->willReturn(false);
        $request->method('getHeaderLine')->willReturn('');
        $request->method('getHeader')->willReturn([]);
        $request->method('getBody')->willReturn($this->createMockStream(''));
        $request->method('withHeader')->willReturnSelf();
        $request->method('withBody')->willReturnSelf();

        return $request;
    }

    /**
     * Mock response with given status code and body.
     * 
     * @param int $statusCode
     * @param string $body
     * 
     * @return ResponseInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createMockResponse(int $statusCode, string $body): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('getBody')->willReturn($this->createMockStream($body));
        $response->method('getProtocolVersion')->willReturn('1.1');
        
        return $response;
    }

    /**
     * Mock stream data with given content.
     * 
     * @param string $content
     * 
     * @return StreamInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createMockStream(string $content): StreamInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')->willReturn($content);
        $stream->method('__toString')->willReturn($content);
        
        return $stream;
    }
}
