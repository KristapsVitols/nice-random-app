<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\AccountTransactionException;
use App\Models\AccountTransaction;
use App\Repositories\AccountTransactionRepository;

readonly class AccountTransactionRetrievalService
{
    public function __construct(private AccountTransactionRepository $accountTransactionRepository)
    {
    }

    /**
     * @throws AccountTransactionException
     */
    public function getTransactionsByAccountId(int $accountId, int $offset, int $limit): array
    {
        if (!$accountId) {
            throw new AccountTransactionException('Please provide account id!');
        }

        return array_map(
            fn(AccountTransaction $transaction) => $transaction->toApiData(),
            $this->accountTransactionRepository->getTransactionsByAccountId($accountId, $offset, $limit)
        );
    }
}
