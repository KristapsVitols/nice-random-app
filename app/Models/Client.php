<?php

declare(strict_types=1);

namespace App\Modules\Client\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 *
 * @mixin Builder
 */
class Client extends Model
{
}
