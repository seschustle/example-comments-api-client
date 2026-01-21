# Imaginary comments service API Client

## Overview
**[PSR-18](https://github.com/php-fig/http-client)** and **[PSR-17](https://github.com/php-fig/http-factory)** complient API Client implementation for imaginary comments service `https://example.com`.

Requires PHP 8.2 or higher.

## Quick Start

### Installation

```bash
composer require seschustle/example-comments-api-client
```
### Basic Usage

> [!NOTE]
> API key is not required for current use since imaginary service does not really have actual authentication. Just pass any string as a key.

```php
<?php

use seschustle\ExampleCommentsApiClient\Client;

class YourClass {
    // Your code goes here
    // ...
    // ...
    
    $client = new Client('your-api-token');

    // Get all comments
    $comments = $client->listComments();

    // Create new comment
    $newComment = $client->createComment(['name' => 'John Doe', 'text' => 'Some comment']);

    // Update existing comment
    $updatedComment = $client->updateComment(1, ['name' => 'John Doe', 'text' => 'Edited comment']);
    
    // ...
    // ...
    // Your other code
}
```

### Core documentation
```php
Comment::class
// Simple DTO for a comment. Has required `id`, `name` and `text` fields.
```

```php
Client::listComments(): Comment[]
// Returns an array of Comment DTO instances.
```

```php
Client::createComment(array $fields): array
// Creates a comment through POST HTTP request to the API. No fields validation, this part is delegated to a service.
// Returns created comment data.
```

```php
Client::updateComment(int $id, array $fields): array
// Update a comment through PUT HTTP request to the API. No fields validation, this part is delegated to a service.
// Returns updated comment data.
```

### Using with custom dependencies

You can create `Client` instance with your own `HttpClient`, `RequestFactory` and `StreamFactory` imlementations if you prefer. If they are not passed, they will be tried to be discovered by `php-http/discovery`.

```php
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Http\Discovery\Psr17Factory;

$httpClient = new Psr18Client(new CurlHttpClient());
$requestFactory = new Psr17Factory();
$streamFactory = new Psr17Factory();

$client = new Client(
    'your-api-token',
    $httpClient,
    $requestFactory,
    $streamFactory
);
```

## License

MIT License. See [LICENSE](LICENSE) file for details.

## Author

Pavel Lovkii <plovkiy@yandex.ru>

---

**Version**: 1.0.0  
**Last Updated**: 2026-01-22  
**PHP Version**: 8.2+
