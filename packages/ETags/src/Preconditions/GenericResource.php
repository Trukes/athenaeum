<?php

namespace Aedart\ETags\Preconditions;

use Aedart\Contracts\ETags\ETag;
use Aedart\Contracts\ETags\Preconditions\ResourceContext;
use DateTimeInterface;

/**
 * Generic Resource
 *
 * @see \Aedart\Contracts\ETags\Preconditions\ResourceContext
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\ETags
 */
class GenericResource implements ResourceContext
{
    /**
     * Create a new "generic" resource
     *
     * @param  ETag|null  $etag  [optional]
     * @param  DateTimeInterface|null  $lastModifiedDate  [optional]
     * @param  int  $size  [optional]
     */
    public function __construct(
        protected ETag|null $etag = null,
        protected DateTimeInterface|null $lastModifiedDate = null,
        protected int $size = 0
    ) {}

    /**
     * @inheritDoc
     */
    public function etag(): ETag|null
    {
        return $this->etag;
    }

    /**
     * @inheritDoc
     */
    public function hasEtag(): bool
    {
        return isset($this->etag);
    }

    /**
     * @inheritDoc
     */
    public function lastModifiedDate(): DateTimeInterface|null
    {
        return $this->lastModifiedDate;
    }

    /**
     * @inheritDoc
     */
    public function hasLastModifiedDate(): bool
    {
        return isset($this->lastModifiedDate);
    }

    /**
     * @inheritDoc
     */
    public function size(): int
    {
        return $this->size;
    }
}