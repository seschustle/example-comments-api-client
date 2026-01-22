<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient\Tests\Unit\Client;

use seschustle\ExampleCommentsApiClient\Comment;
use seschustle\ExampleCommentsApiClient\Exception\ApiRequestException;
use seschustle\ExampleCommentsApiClient\Exception\DTOCreationException;
use seschustle\ExampleCommentsApiClient\Exception\InvalidApiResponseException;
use seschustle\ExampleCommentsApiClient\Tests\Fixtures\CommentsData;
use seschustle\ExampleCommentsApiClient\Tests\Fixtures\HttpErrorBody;

/**
 * Test cases for Client::createComment() and Client::updateComment() methods.
 */
class CreateAndUpdateCommentTest extends ClientTestCase
{
    /**
     * Successful comment creation, happy path.
     * 
     * @return void
     */
    public function testCreateSuccess(): void
    {
        $this->prepareClient(201, CommentsData::validSingleComment());
        
        $comment = $this->client->createComment(CommentsData::validCreateAndUpdateData());

        $this->assertInstanceOf(Comment::class, $comment);
        // @TODO check comment fields
    }

    /**
     * Successful comment update, happy path.
     * 
     * @return void
     */
    public function testUpdateSuccess(): void
    {
        $this->prepareClient(200, CommentsData::validSingleComment());
        
        $comment = $this->client->updateComment(1, CommentsData::validCreateAndUpdateData());

        $this->assertSame(CommentsData::validSingleComment()['name'], $comment->getName());
        $this->assertSame(CommentsData::validSingleComment()['text'], $comment->getText());
    }

    /**
     * Successful comment creation with partial data, happy path.
     * 
     * @return void
     */
    public function testUpdateWithPartialDataSuccess(): void
    {
        $this->prepareClient(200, CommentsData::validSingleComment());
        
        $comment = $this->client->updateComment(1, CommentsData::valiPartialUpdateData());

        $this->assertSame(CommentsData::validSingleComment()['name'], $comment->getName());
        $this->assertSame(CommentsData::validSingleComment()['text'], $comment->getText());
    }

    /**
     * Invalid body given, expects exception.
     * 
     * @return void
     */
    public function testInvalidDataPriovided(): void
    {
        $this->prepareClient(422, HttpErrorBody::unpocessableEntity());
        $this->expectException(ApiRequestException::class);
        $this->expectExceptionCode(422);
        $this->expectExceptionMessage('API returned HTTP 422 status code. Response: {"code":422,"message":"Unprocessable entity, check payload."}');
        
        $this->client->createComment(CommentsData::valiPartialUpdateData());
    }

    /**
     * Udpdate non-existent comment, expects exception.
     *
     * @return void
     */
    public function testInexistentCommentUpdate(): void
    {
        $this->prepareClient(404, HttpErrorBody::notFound());
        $this->expectException(ApiRequestException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('API returned HTTP 404 status code. Response: {"code":404,"message":"Page not found, check URL."}');
        
        $this->client->updateComment(999, CommentsData::valiPartialUpdateData());
    }

    /**
     * Mock response and client with given awaited response and status code.
     *
     * @param array $awaitedResponse
     *
     * @return void
     */
    private function prepareClient(int $status, array $awaitedResponse): void
    {
        $response = $this->createMockResponse($status, json_encode($awaitedResponse));
        $this->httpClient->method('sendRequest')->willReturn($response);
    }
}
