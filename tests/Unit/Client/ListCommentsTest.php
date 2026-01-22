<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient\Tests\Unit\Client;

use seschustle\ExampleCommentsApiClient\Exception\DTOCreationException;
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
     * Invalid comments found case, expects exception.
     * 
     * @return void
     */
    public function testListCommentsHasInvalidComments(): void
    {
        $this->prepareClient(CommentsData::multipleCommentsContaintsInvalid());

        $comments = $this->client->listComments();

        $this->expectException(DTOCreationException::class);
    }

    private function prepareClient(array $awaitedResponse): void
    {
        $response = $this->createMockResponse(200, json_encode($awaitedResponse));
        $this->httpClient->method('sendRequest')->willReturn($response);
    }
}
