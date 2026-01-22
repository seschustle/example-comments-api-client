<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient\Tests\Fixtures;

class HttpErrorBody
{
    public static function unpocessableEntity(): array
    {
        return [
            'code' => 422,
            'message'=> 'Unprocessable entity, check payload.',
        ];
    }

    public static function notFound(): array
    {
        return [
            'code' => 404,
            'message'=> 'Page not found, check URL.',
        ];
    }

    public static function putNotAllowed(): array
    {
        return [
            'code' => 405,
            'message'=> 'Method not allowed. Allowed: \'GET\', \'POST\'.',
        ];
    }

    public static function onlyPutAllowed(): array
    {
        return [
            'code' => 405,
            'message'=> 'Method not allowed. Allowed: \'PUT\'.',
        ];
    }
}
