<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccountTransaction;
use App\Repositories\AccountTransactionRepository;

class AccountTransactionManagementService
{
    public function __construct(readonly private AccountTransactionRepository $accountTransactionRepository)
    {
    }

    public function createTransaction(
        int    $accountId,
        string $type,
        float  $amount,
        string $currency,
        int    $referenceAccountId = null
    ): AccountTransaction
    {
        return $this->accountTransactionRepository->createTransaction(
            $accountId,
            $type,
            $amount,
            $currency,
            $referenceAccountId
        );
    }
}
