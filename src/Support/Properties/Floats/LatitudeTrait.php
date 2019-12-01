<?php

namespace Aedart\Support\Properties\Floats;

/**
 * Latitude Trait
 *
 * @see \Aedart\Contracts\Support\Properties\Floats\LatitudeAware
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Support\Properties\Floats
 */
trait LatitudeTrait
{
    /**
     * North-South position on Earth&#039;s surface
     *
     * @var float|null
     */
    protected ?float $latitude = null;

    /**
     * Set latitude
     *
     * @param float|null $value North-South position on Earth&#039;s surface
     *
     * @return self
     */
    public function setLatitude(?float $value)
    {
        $this->latitude = $value;

        return $this;
    }

    /**
     * Get latitude
     *
     * If no "latitude" value set, method
     * sets and returns a default "latitude".
     *
     * @see getDefaultLatitude()
     *
     * @return float|null latitude or null if no latitude has been set
     */
    public function getLatitude() : ?float
    {
        if ( ! $this->hasLatitude()) {
            $this->setLatitude($this->getDefaultLatitude());
        }
        return $this->latitude;
    }

    /**
     * Check if "latitude" has been set
     *
     * @return bool True if "latitude" has been set, false if not
     */
    public function hasLatitude() : bool
    {
        return isset($this->latitude);
    }

    /**
     * Get a default "latitude" value, if any is available
     *
     * @return float|null Default "latitude" value or null if no default value is available
     */
    public function getDefaultLatitude() : ?float
    {
        return null;
    }
}
