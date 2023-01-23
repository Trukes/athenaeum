<?php

namespace Aedart\Validation\Rules;

use Aedart\Contracts\Support\Helpers\Translation\TranslatorAware;
use Illuminate\Contracts\Validation\Rule;

/**
 * Base Validation Rule
 *
 * Abstraction that offers common utility methods for
 * validation rules.
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Acl\Rules
 */
abstract class BaseRule implements
    Rule,
    TranslatorAware
{
    use Concerns\Attribute;
    use Concerns\Translations;
}
