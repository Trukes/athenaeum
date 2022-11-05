<?php

namespace Aedart\ETags\Generators;

use Aedart\Contracts\ETags\ETag;
use Aedart\Contracts\ETags\Generator;
use Aedart\ETags\Exceptions\UnableToGenerateETag;
use Aedart\ETags\Facades\Generator as GeneratorFacade;
use Throwable;

/**
 * Base ETag Generator
 *
 * Abstraction for ETag generators.
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\ETags\Generators
 */
abstract class BaseGenerator implements Generator
{
    /**
     * Name of the default hashing algorithm
     */
    protected const DEFAULT_HASH_ALGO = 'sha1';

    /**
     * Create a new ETag Generator instance
     *
     * @param  array  $options  [optional]
     */
    public function __construct(
        protected array $options = []
    ) {}

    /**
     * @inheritDoc
     */
    public function make(mixed $content): ETag
    {
        try {
            $rawValue = $this->hashRawValue(
                $this->resolveContent($content)
            );

            $isWeak = $this->mustMarkAsWeak();

            return $this->makeETag($rawValue, $isWeak);
        } catch (Throwable $e) {
            throw new UnableToGenerateETag($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Resolves the content to be hashed and used as ETag's raw value
     *
     * @param  mixed  $content
     *
     * @return string
     *
     * @throws UnableToGenerateETag
     */
    abstract public function resolveContent(mixed $content): string;

    /**
     * Builds an ETag's raw value from given content
     *
     * @param  string  $content
     *
     * @return string
     */
    protected function hashRawValue(string $content): string
    {
        return $this->hash($content, $this->hashAlgorithm());
    }

    /**
     * Determine if generated ETag must be marked as "weak"
     *
     * @return bool
     */
    protected function mustMarkAsWeak(): bool
    {
        return $this->options['is_weak'] ?? false;
    }

    /**
     * Returns a hash of given content, using specified hashing algorithm
     *
     * @param  string  $content
     * @param  string  $algorithm
     *
     * @return string
     */
    protected function hash(string $content, string $algorithm): string
    {
        return hash($algorithm, $content);
    }

    /**
     * Returns name of the hashing algorithm to use
     *
     * @return string
     */
    protected function hashAlgorithm(): string
    {
        return $this->options['hash_algo'] ?? static::DEFAULT_HASH_ALGO;
    }

    /**
     * Creates a new ETag instance for the raw value
     *
     * @param  string  $rawValue
     * @param  bool  $weak  [optional]
     *
     * @return ETag
     */
    protected function makeETag(string $rawValue, bool $weak = false): ETag
    {
        return GeneratorFacade::makeRaw($rawValue, $weak);
    }
}