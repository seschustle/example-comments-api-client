<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient\Tests\Fixtures;

/**
 * Predefines comments test data used in tests.
 */
class CommentsData
{
    public static function validSingleComment(): array
    {
        return [
            'id' => 99,
            'name' => 'John Doe',
            'text' => 'Great article!',
        ];
    }

    public static function validMultipleComments(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'John Doe',
                'text' => 'First comment',
            ],
            [
                'id' => 2,
                'name' => 'Jane Smith',
                'text' => 'Like the article',
            ],
            [
               'id' => 3,
               'name' => 'Bob Johnson',
               'text' => 'Thanks for sharing!',
            ],
            [
                'id'=> 4,
                'name'=> 'John Doe',
                'text'=> 'Hey everyone',
            ]
        ];
    }

    public static function validSingleCommentInArray(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'John Doe',
                'text' => 'First comment',
            ],
        ];
    }

    public static function validEmptyCommentsArray(): array
    {
        return [];
    }

    public static function invalidIdSingleComment(): array
    {
        return [
            'id' => -1,
            'name' => 'John Doe',
            'text' => 'First comment',
        ];
    }

    public static function invalidSingleCommentMissingData(): array
    {
        return [
            'id' => 1,
            'text' => 'First comment',
        ];
    }

    public static function invalidSingleCommentEmptyRequiredField(): array
    {
        return [
            'id' => 1,
            'name' => 'Jane Smith',
            'text' => '',
        ];
    }

    public static function multipleCommentsContaintsInvalid(): array
    {
        return [
            self::validSingleComment(),
            self::invalidSingleCommentMissingData(),
        ];
    }

    public static function validCreateAndUpdateData(): array
    {
        return [
            'name' => 'John Doe',
            'text' => 'This is a great comment!',
        ];
    }

    public static function valiPartialUpdateData(): array
    {
        return [
            'text' => 'I can edit the comments!',
        ];
    }
}
