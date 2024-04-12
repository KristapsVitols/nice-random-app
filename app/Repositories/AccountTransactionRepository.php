<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\AccountTransaction;

class AccountTransactionRepository
{
    /**
     * @param int $accountId
     * @param int $offset
     * @param int $limit
     * @return AccountTransaction[]|array
     */
    public function getTransactionsByAccountId(int $accountId, int $offset, int $limit): array
    {
        return AccountTransaction::where(['client_account_id' => $accountId])
            ->orderBy('id', 'DESC')
            ->skip($offset)
            ->limit($limit ?: 5)
            ->get()
            ->all();
    }

    public function createTransaction(
        int    $accountId,
        string $transactionType,
        float  $transactionAmount,
        string $transactionCurrency,
        ?int   $referenceAccountId
    ): AccountTransaction
    {
        $transaction = new AccountTransaction();

        $transaction->client_account_id = $accountId;
        $transaction->transaction_type = $transactionType;
        $transaction->transaction_amount = $transactionAmount;
        $transaction->transaction_currency = $transactionCurrency;
        $transaction->reference_account_id = $referenceAccountId;

        $transaction->save();

        return $transaction;
    }
}
