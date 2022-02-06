<?php

namespace Aedart\Contracts\Support\Properties\Strings;

/**
 * Delivered at Aware
 *
 * Component is aware of string "delivered at"
 *
 * @author Alin Eugen Deac <aedart@gmail.com>
 * @package Aedart\Contracts\Support\Properties\Strings
 */
interface DeliveredAtAware
{
    /**
     * Set delivered at
     *
     * @param string|null $date Date of delivery
     *
     * @return self
     */
    public function setDeliveredAt(string|null $date): static;

    /**
     * Get delivered at
     *
     * If no delivered at value set, method
     * sets and returns a default delivered at.
     *
     * @see getDefaultDeliveredAt()
     *
     * @return string|null delivered at or null if no delivered at has been set
     */
    public function getDeliveredAt(): string|null;

    /**
     * Check if delivered at has been set
     *
     * @return bool True if delivered at has been set, false if not
     */
    public function hasDeliveredAt(): bool;

    /**
     * Get a default delivered at value, if any is available
     *
     * @return string|null Default delivered at value or null if no default value is available
     */
    public function getDefaultDeliveredAt(): string|null;
}
