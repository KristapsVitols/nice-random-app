<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $client_account_id
 * @property int|null $reference_account_id
 * @property string $transaction_type
 * @property float $transaction_amount
 * @property string $transaction_currency
 * @property string $created_at
 * @property string $updated_at
 *
 * @mixin Builder
 */
class AccountTransaction extends Model
{
    use HasFactory;

    public const TRANSACTION_TYPE_DEPOSIT = 'deposit';
    public const TRANSACTION_TYPE_WITHDRAWAL = 'withdrawal';
    public const TRANSACTION_TYPE_TRANSFER = 'transfer';

    public function toApiData(): array
    {
        $apiData = [
            'transactionId' => $this->id,
            'clientAccountId' => $this->client_account_id,
            'type' => $this->transaction_type,
            'amount' => $this->transaction_amount,
            'currency' => $this->transaction_currency,
            'createdAt' => $this->created_at,
        ];

        if ($this->reference_account_id) {
            $apiData['referenceAccountId'] = $this->reference_account_id;
        }

        return $apiData;
    }
}
