<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient\Tests\Unit\Client;

use seschustle\ExampleCommentsApiClient\Exception\InvalidApiResponseException;
use seschustle\ExampleCommentsApiClient\Tests\Fixtures\CommentsData;

/**
 * Test cases for Client::listComments() method.
 */
class ListCommentsTest extends ClientTestCase
{
    /**
     * No comments found case, happy path.
     *
     * @return void
     */
    public function testListCommentsIsEmpty(): void
    {
        $this->prepareClient(CommentsData::validEmptyCommentsArray());

        $comments = $this->client->listComments();

        $this->assertIsArray($comments);
        $this->assertCount(0, $comments);
    }

    /**
     * Only one comment found case, happy path.
     *
     * @return void
     */
    public function testListCommentsHasSingleComment(): void
    {
        $this->prepareClient(CommentsData::validSingleCommentInArray());

        $comments = $this->client->listComments();

        $this->assertIsArray($comments);
        $this->assertCount(1, $comments);
    }

    /**
     * Deafult case, multiple comments found, happy path.
     *
     * @return void
     */
    public function testListCommentsHasMultipleComments(): void
    {
        $this->prepareClient(CommentsData::validMultipleComments());

        $comments = $this->client->listComments();

        $this->assertIsArray($comments);
        $this->assertCount(count(CommentsData::validMultipleComments()), $comments);
    }

    /**
     * Invalid JSON returned from server, expects exception.
     *
     * @return void
     */
    public function testMalformedJsonReturned(): void
    {
        $response = $this->createMockResponse(200, json_encode(CommentsData::malformedJson()));
        $this->httpClient->method('sendRequest')->willReturn($response);

        $this->expectException(InvalidApiResponseException::class);
        $this->expectExceptionMessage('Invalid JSON response received from API');

        $this->client->listComments();   
    }

    /**
     * Invalid comments found case, expects exception.
     *
     * @return void
     */
    public function testListCommentsHasInvalidComments(): void
    {
        $this->prepareClient(CommentsData::multipleCommentsContaintsInvalid());
        $this->expectException(InvalidApiResponseException::class);
        $this->expectExceptionMessage('Failed to create Comment DTO from a reponse data');

        $this->client->listComments();
    }

    /**
     * Mock response and client with given awaited response.
     *
     * @param array $awaitedResponse
     *
     * @return void
     */
    private function prepareClient(array $awaitedResponse): void
    {
        $response = $this->createMockResponse(200, json_encode($awaitedResponse));
        $this->httpClient->method('sendRequest')->willReturn($response);
    }
}
