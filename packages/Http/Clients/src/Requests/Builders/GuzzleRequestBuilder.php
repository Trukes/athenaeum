<?php

namespace Aedart\Http\Clients\Requests\Builders;

use Aedart\Contracts\Http\Clients\Client;
use Aedart\Contracts\Http\Clients\Requests\Builder;
use Aedart\Http\Clients\Requests\Builders\Guzzle\Pipes\ResolvesRequestPayload;
use Aedart\Http\Clients\Requests\Builders\Pipes\MergeWithBuilderOptions;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Guzzle Http Request Builder
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Http\Clients\Requests\Builders
 */
class GuzzleRequestBuilder extends BaseBuilder
{
    /**
     * The data format to use
     *
     * @var string
     */
    protected string $dataFormat = RequestOptions::FORM_PARAMS;

    /**
     * GuzzleRequestBuilder constructor.
     *
     * @param Client $client
     * @param array $options [optional] Guzzle Request Options
     */
    public function __construct(Client $client, array $options = [])
    {
        parent::__construct($client, $options);

        $this
            ->extractHeadersFromOptions()
            ->extractDataFormatFromOptions()
            ->setPrepareOptionsPipes([
                MergeWithBuilderOptions::class,
                ResolvesRequestPayload::class
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method = null, $uri = null, array $options = []): ResponseInterface
    {
        $method = $method ?? $this->getMethod();
        $uri = $uri ?? $this->getUri();

        // Resolve options for this request
        // NOTE: We should NOT use the withOptions() method here, as it will
        // be applied for entire builder.
        $options = $this->prepareDriverOptions($options);

        return $this->send(
            $this->createRequest($method, $uri),
            $options
        );
    }

    /**
     * @inheritDoc
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request(
            $method,
            $uri,
            $this->getHeaders(),
            null, // TODO
            $this->getProtocolVersion(),
        );
    }

    /**
     * @inheritdoc
     */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        return $this->driver()->send(
            $request,
            $options
        );
    }

    /**
     * @inheritDoc
     */
    public function formFormat(): Builder
    {
        return $this
            ->useDataFormat('form_params')
            ->withContentType('application/x-www-form-urlencoded');
    }

    /**
     * @inheritDoc
     */
    public function jsonFormat(): Builder
    {
        return $this
            ->useDataFormat('json')
            ->withAccept($this->jsonAccept)
            ->withContentType($this->jsonContentType);
    }

    /**
     * @inheritDoc
     */
    public function multipartFormat(): Builder
    {
        return $this
            ->useDataFormat('multipart')
            ->withContentType('multipart/form-data');
    }

    /**
     * @inheritDoc
     */
    public function useBasicAuth(string $username, string $password): Builder
    {
        return $this->withOption('auth', [ $username, $password ]);
    }

    /**
     * @inheritDoc
     */
    public function useDigestAuth(string $username, string $password): Builder
    {
        return $this->withOption('auth', [ $username, $password, 'digest' ]);
    }

    /**
     * @inheritDoc
     */
    public function maxRedirects(int $amount): Builder
    {
        if ($amount === 0) {
            return $this->disableRedirects();
        }

        return $this->withOption('allow_redirects', [
            'max' => $amount,
            'strict' => true,
            'referer' => true,
            'protocols' => ['http', 'https'],
            'track_redirects' => false
        ]);
    }

    /**
     * @inheritDoc
     */
    public function disableRedirects(): Builder
    {
        return $this->withOption('allow_redirects', false);
    }

    /**
     * @inheritDoc
     */
    public function withTimeout(float $seconds): Builder
    {
        return $this->withOption('timeout', $seconds);
    }

    /**
     * @inheritDoc
     */
    public function getTimeout(): float
    {
        return (float) $this->getOption('timeout');
    }

    /**
     * @inheritDoc
     *
     * @return GuzzleClient
     */
    public function driver()
    {
        return parent::driver();
    }

    /*****************************************************************
     * Internals
     ****************************************************************/

    /**
     * Extracts the Http headers from the options into this
     * builder.
     *
     * @return self
     */
    protected function extractHeadersFromOptions()
    {
        $headers = $this->options['headers'] ?? [];

        if (!empty($headers)) {
            $this->withHeaders($headers);
        }

        unset($this->options['headers']);

        return $this;
    }

    /**
     * Extracts the data format from the options and sets
     * the appropriate headers
     *
     * @return self
     */
    protected function extractDataFormatFromOptions()
    {
        $format = $this->options['data_format'] ?? $this->getDataFormat();
        unset($this->options['data_format']);

        switch ($format) {
            case RequestOptions::FORM_PARAMS:
            case RequestOptions::BODY:
                $this->formFormat();
                break;

            case RequestOptions::JSON:
                $this->jsonFormat();
                break;

            case RequestOptions::MULTIPART:
                $this->multipartFormat();
                break;

            default:
                $this->useDataFormat($format);
                break;
        }

        return $this;
    }
}
