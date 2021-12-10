<?php

namespace Aedart\Filters\Query\Filters\Fields;

use Aedart\Utils\Arr;
use InvalidArgumentException;

/**
 * Date Filter
 *
 * @author Alin Eugen Deac <ade@rspsystems.com>
 * @package Aedart\Filters\Query\Filters\Fields
 */
class DateFilter extends BaseFieldFilter
{
    /**
     * @inheritDoc
     */
    public function apply($query)
    {
        $operator = $this->operator();

        switch ($operator) {
            case 'is_null':
                return $this->buildWhereNullConstraint($query);

            case 'not_null':
                return $this->buildWhereNotNullConstraint($query);

            default:
                return $this->buildWhereDateConstraint($query);
        }
    }

    /**
     * @inheritDoc
     */
    public function operatorAliases(): array
    {
        return [
            'eq' => '=',
            'ne' => '!=',
            'gt' => '>',
            'gte' => '>=',
            'lt' => '<',
            'lte' => '<=',

            // NOTE: Values do NOT correspond directly to sql operators for these...
            'is_null' => 'is_null',
            'not_null' => 'not_null',
        ];
    }

    /*****************************************************************
     * Internals
     ****************************************************************/

    /**
     * @inheritDoc
     */
    protected function assertValue($value)
    {
        // Allow empty values, when "is null / not null" operators are
        // chosen.
        if (empty($value) && in_array($this->operator(), [ 'is_null', 'not_null' ])) {
            return;
        }

        $field = $this->field();

        // To ensure that the submitted date is valid, we will rely on Laravel's validator.
        // It's by far the simplest way of ensuring the input is as desired. Note the
        // array-undot call. This is done in case of evt. table name prefixes, then we must
        // ensure that the "data" is associative, or the applied validation rule might fail.
        $validator = $this->getValidatorFactory()->make(Arr::undot([ $field => $value ]), [
            $field => 'required|date_format:' . implode(',', $this->allowedDateFormats())
        ]);

        if ($validator->fails()) {
            $reason = $validator->errors()->first($field);
            throw new InvalidArgumentException($reason);
        }
    }

    /**
     * Returns list of allowed date formats
     *
     * @return string[]
     */
    protected function allowedDateFormats(): array
    {
        return [
            'Y-m-d',
        ];
    }
}
