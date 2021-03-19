<?php

namespace Aedart\Tests\Helpers\Dummies\Acl;

use Aedart\Contracts\Database\Models\Instantiatable;
use Aedart\Database\Models\Concerns;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * User
 *
 * FOR TESTING PURPOSES ONLY
 *
 * @author Alin Eugen Deac <ade@rspsystems.com>
 * @package Aedart\Tests\Helpers\Dummies\Acl
 */
class User extends Authenticatable implements Instantiatable
{
    use Concerns\Instance;
}