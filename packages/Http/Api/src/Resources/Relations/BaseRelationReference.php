<?php

namespace Aedart\Http\Api\Resources\Relations;

use Aedart\Contracts\Http\Api\Resources\Relations\Exceptions\RelationReferenceException as RelationReferenceExceptionInterface;
use Aedart\Contracts\Http\Api\Resources\Relations\RelationReference;
use Aedart\Http\Api\Resources\ApiResource;
use Aedart\Http\Api\Resources\Relations\Concerns;
use Aedart\Http\Api\Resources\Relations\Exceptions\CannotInvokeCallback;
use Aedart\Http\Api\Resources\Relations\Exceptions\RelationReferenceException;
use Aedart\Http\Api\Traits\ApiResourceRegistrarTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Base Relation Reference
 *
 * @see RelationReference
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Http\Api\Resources\Relations
 */
abstract class BaseRelationReference implements RelationReference
{
    use ApiResourceRegistrarTrait;
    use Concerns\PrimaryKey;
    use Concerns\Label;
    use Concerns\ResourceType;
    use Concerns\SelfLink;
    use Concerns\AdditionalFormatting;

    /**
     * Default value to use when relation not loaded
     *
     * @var callable|mixed
     */
    protected $defaultValue = null;

    /**
     * Callback to be applied on loaded relation
     *
     * @var callable|null
     */
    protected $whenLoadedCallback = null;

    /**
     * Current request
     *
     * @var Request|null
     */
    protected Request|null $request = null;

    /**
     * Creates a new relation reference
     *
     * @param  \Aedart\Http\Api\Resources\ApiResource|mixed  $resource
     * @param  string  $relation Name of eloquent model relation
     */
    public function __construct(
        protected mixed $resource,
        protected string $relation
    ){}

    /**
     * @inheritDoc
     */
    public function toValue(): mixed
    {
        $relation = $this->getEagerLoadedRelation();

        // Return the "default" value when relation is not
        // loaded in the eloquent model
        if (!isset($relation)) {
            return $this->resolveDefaultValue();
        }

        // Apply callback on relation and resolve the value
        // this relation reference represents.
        return $this->applyWhenLoaded($relation);
    }

    /**
     * @inheritDoc
     */
    public function whenLoaded(callable $callback): static
    {
        $this->whenLoadedCallback = $callback;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function otherwise($default = null): static
    {
        return $this->defaultTo($default);
    }

    /**
     * @inheritDoc
     */
    public function defaultTo($default = null): static
    {
        $this->defaultValue = $default;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withRequest($request): static
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function getApiResource()
    {
        return $this->resource;
    }

    /**
     * @inheritDoc
     */
    public function getModel()
    {
        return $this->getApiResource()->resource;
    }

    /**
     * @inheritDoc
     */
    public function getEagerLoadedRelation()
    {
        $name = $this->getRelationName();
        $model = $this->getModel();

        if (!isset($model)) {
            return null;
        }

        // TODO: What if requested relation is nested, e.g. using dot syntax?!

        if ($model->relationLoaded($name) && isset($model->{$name})) {
            return $model->getRelation($name);
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getRelationName(): string
    {
        return $this->relation;
    }

    /*****************************************************************
     * Default Formatting of Loaded Model(s)
     ****************************************************************/

    /**
     * Formats a single relation model as this reference's value
     *
     * @param  Model  $relation
     * @param  static  $relationReference
     *
     * @return mixed Formatted reference value
     *
     * @throws RelationReferenceExceptionInterface
     */
    public function formatSingleLoadedModel(Model $relation, $relationReference): mixed
    {
        // Obtain the relation's primary identifier and return it directly,
        // when relation must be formatted as a primitive value.
        $identifier = $this->resolveIdentifier($relation, $relationReference->getPrimaryKeyName(), $relationReference);

        if ($relationReference->mustReturnRawIdentifier()) {
            return $identifier;
        }

        // Format reference's value. Obtain attributes to be displayed.
        $output = $this->addRelatedModelPrimaryKey([], $identifier, $relation, $relationReference);

        // Add label, if needed
        $output = $this->addLabel($output, $relation, $relationReference);

        // Show Resource Type, if needed
        $output = $this->addResourceType($output, $relation, $relationReference);

        // Show self link,...
        $output = $this->addSelfLink($output, $relation, $relationReference);

        // Finally, apply an evt. "additional" callback that allows developer
        // to add or change the final output entirely.
        return $this->applyAdditionalFormatting($output, $relation, $relationReference);
    }

    /*****************************************************************
     * Internals
     ****************************************************************/

    /**
     * Invokes the "when loaded" callback on given relation
     *
     * @param  Model|Collection  $loadedRelation
     *
     * @return mixed
     *
     * @throws RelationReferenceExceptionInterface
     */
    protected function applyWhenLoaded(Model|Collection $loadedRelation): mixed
    {
        $whenLoaded = $this->whenLoadedCallback;

        if (!is_callable($whenLoaded)) {
            throw new CannotInvokeCallback(sprintf(
                '"When loaded" callback is not callable, for "%s" relation in %s Api Resource',
                $this->getRelationName(),
                optional($this->getApiResource())->type() ?? 'unknown'
            ));
        }

        return $whenLoaded($loadedRelation, $this);
    }

    /**
     * Resolves the default value
     *
     * @return mixed
     */
    protected function resolveDefaultValue(): mixed
    {
        $value = $this->defaultValue;

        if (is_callable($value)) {
            return $value($this);
        }

        return $value;
    }

    /**
     * Find the corresponding Api Resource for given related eloquent model, or fail
     *
     * @param  Model  $relation
     * @param  static|null $relationReference  [optional]
     *
     * @return ApiResource
     *
     * @throws RelationReferenceExceptionInterface
     */
    protected function findApiResourceOrFail(Model $relation, $relationReference = null): ApiResource
    {
        $resourceClass = $this->getApiResourceRegistrar()->get($relation);

        if (!isset($resourceClass)) {
            $relationReference = $relationReference ?? $this;

            throw new RelationReferenceException(sprintf(
                'No matching Api Resource found for "%s" relation, in %s resource',
                $relationReference->getRelationName(),
                $relationReference->getApiResource()->type()
            ));
        }

        return $resourceClass::make($relation);
    }
}