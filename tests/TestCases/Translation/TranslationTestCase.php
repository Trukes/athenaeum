<?php

namespace Aedart\Tests\TestCases\Translation;

use Aedart\Http\Api\Providers\JsonResourceServiceProvider;
use Aedart\Support\Helpers\Translation\TranslatorTrait;
use Aedart\Testing\TestCases\LaravelTestCase;
use Aedart\Tests\Helpers\Dummies\Translation\AcmeTranslationsServiceProvider;

/**
 * TranslationTestCase
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Tests\TestCases\Translation
 */
abstract class TranslationTestCase extends LaravelTestCase
{
    use TranslatorTrait;

    /*****************************************************************
     * Setup
     ****************************************************************/

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            // Packages that publishes or load translations...
            JsonResourceServiceProvider::class,
            AcmeTranslationsServiceProvider::class
        ];
    }
}