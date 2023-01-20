<?php

namespace Aedart\Filters\Query\Filters;

use Aedart\Database\Concerns\Prefixing;
use Aedart\Database\Query\Concerns\Joins;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\Database\Query\Builder;

/**
 * Base Search Query
 *
 * @author Alin Eugen Deac <ade@rspsystems.com>
 * @package Aedart\Filters\Query
 */
abstract class BaseSearchQuery
{
    use Prefixing;
    use Joins;

    /**
     * Creates a new search query instance
     *
     * @param string|null $tablePrefix [optional] Evt. table name for columns prefixing
     */
    public function __construct(
        protected string|null $tablePrefix = null
    ) {}

    /**
     * Returns table prefix
     *
     * @return string|null
     */
    public function tablePrefix(): string|null
    {
        return $this->tablePrefix;
    }

    /**
     * Builds a search query for the given search term
     *
     * @param Builder|EloquentBuilder $query
     * @param string $search
     *
     * @return Builder|EloquentBuilder
     */
    abstract public function __invoke(Builder|EloquentBuilder $query, string $search): Builder|EloquentBuilder;
}