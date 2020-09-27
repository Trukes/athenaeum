<?php

namespace Aedart\Http\Messages\Serializers;

use Aedart\Contracts\Http\Messages\Serializers\ResponseSerializer as ResponseSerializerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Response Serializer
 *
 * @see \Aedart\Contracts\Http\Messages\Serializers\ResponseSerializer
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Http\Messages\Serializers
 */
class ResponseSerializer extends BaseSerializer implements ResponseSerializerInterface
{
    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $response = $this->getHttpResponse();

        return [
            'status' => $response->getStatusCode(),
            'reason' => $response->getReasonPhrase(),
            'protocol_version' => $response->getProtocolVersion(),
            'headers' => $response->getHeaders(),
            'body' => (string) $response->getBody()
        ];
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        $response = $this->getHttpResponse();

        $format = 'HTTP/%s %d %s%s%s';
        $protocol = $response->getProtocolVersion();
        $status = $response->getStatusCode();
        $reason = $response->getReasonPhrase() . PHP_EOL;
        $headers = $this->serialiseHeaders($response->getHeaders()) . str_repeat(PHP_EOL, 2);
        $body = (string) $response->getBody();

        return sprintf(
            $format,
            $protocol,
            $status,
            $reason,
            $headers,
            $body
        );
    }

    /**
     * @inheritDoc
     */
    public function setHttpResponse(?ResponseInterface $response)
    {
        return $this->setHttpMessage($response);
    }

    /**
     * @inheritDoc
     */
    public function getHttpResponse(): ?ResponseInterface
    {
        return $this->getHttpMessage();
    }

    /**
     * @inheritDoc
     */
    public function hasHttpResponse(): bool
    {
        return $this->hasHttpMessage();
    }

    /**
     * @inheritDoc
     */
    public function getDefaultHttpResponse(): ?ResponseInterface
    {
        return $this->getDefaultHttpMessage();
    }
}
