<?php

declare(strict_types=1);

namespace AndiSiahaan\Digiflazz\Contracts;

use Psr\Http\Message\ResponseInterface;

/**
 * HTTP client interface for abstraction over HTTP implementations.
 */
interface HttpClientInterface
{
    /**
     * Send a POST request.
     *
     * @param string $uri Request URI
     * @param array<string, mixed> $options Request options
     * @return ResponseInterface PSR-7 response
     */
    public function post(string $uri, array $options = []): ResponseInterface;
}
