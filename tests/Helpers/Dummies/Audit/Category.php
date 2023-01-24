<?php


namespace Aedart\Tests\Helpers\Dummies\Audit;

use Aedart\Audit\Concerns\ChangeRecoding;
use Aedart\Tests\Helpers\Dummies\Database\Models\Category as BaseCategory;

/**
 * Category
 *
 * FOR TESTING ONLY
 *
 * @see \Aedart\Tests\Helpers\Dummies\Database\Models\Category
 *
 * @author Alin Eugen Deac <ade@rspsystems.com>
 * @package Aedart\Tests\Helpers\Dummies\Audit
 */
class Category extends BaseCategory
{
    use ChangeRecoding;

    /**
     * @inheritdoc
     */
    public function getAuditTrailMessage(string $type): ?string
    {
        return "Recording {$type} event";
    }
}
