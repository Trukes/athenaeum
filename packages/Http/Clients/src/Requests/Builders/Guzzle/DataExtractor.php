<?php


namespace Aedart\Http\Clients\Requests\Builders\Guzzle;


use GuzzleHttp\RequestOptions;

/**
 * Data Extractor
 *
 * Utility that extracts a request's payload data from
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Http\Clients\Requests\Builders\Guzzle
 */
class DataExtractor
{
    /**
     * Extracts payload data from given driver options
     *
     * @param array $options
     *
     * @return array
     */
    public static function extract(array $options): array
    {
        $targets = [
            RequestOptions::FORM_PARAMS,
            RequestOptions::BODY,
            RequestOptions::JSON,
            RequestOptions::MULTIPART
        ];

        $output = [];
        foreach ($targets as $key){
            $output = static::mergeIfExists($key, $options, $output);
        }

        return $output;
    }

    /**
     * Merge data from given array into target array, if key exists
     *
     * @param string $key
     * @param array $from
     * @param array $target
     *
     * @return array
     */
    protected static function mergeIfExists(string $key, array $from, array $target): array
    {
        if(array_key_exists($key, $from)){
            return $target = array_merge($target, $from[$key]);
        }

        return $target;
    }
}