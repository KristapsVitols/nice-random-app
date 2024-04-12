<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $client_id
 * @property float $balance
 * @property string $currency
 * @property string $created_at
 * @property string $updated_at
 *
 * @mixin Builder
 */
class ClientAccount extends Model
{
    use HasFactory;

    public function transactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class);
    }

    public function toApiData(): array
    {
        return [
            'accountId' => $this->id,
            'clientId' => $this->client_id,
            'balance' => $this->balance,
            'currency' => $this->currency,
        ];
    }
}
