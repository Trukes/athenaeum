<?php

namespace Aedart\Support\Properties\Integers;

/**
 * Age Trait
 *
 * @see \Aedart\Contracts\Support\Properties\Integers\AgeAware
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Support\Properties\Integers
 */
trait AgeTrait
{
    /**
     * Age of someone or something
     *
     * @var int|null
     */
    protected ?int $age = null;

    /**
     * Set age
     *
     * @param int|null $age Age of someone or something
     *
     * @return self
     */
    public function setAge(?int $age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * If no "age" value set, method
     * sets and returns a default "age".
     *
     * @see getDefaultAge()
     *
     * @return int|null age or null if no age has been set
     */
    public function getAge() : ?int
    {
        if ( ! $this->hasAge()) {
            $this->setAge($this->getDefaultAge());
        }
        return $this->age;
    }

    /**
     * Check if "age" has been set
     *
     * @return bool True if "age" has been set, false if not
     */
    public function hasAge() : bool
    {
        return isset($this->age);
    }

    /**
     * Get a default "age" value, if any is available
     *
     * @return int|null Default "age" value or null if no default value is available
     */
    public function getDefaultAge() : ?int
    {
        return null;
    }
}
