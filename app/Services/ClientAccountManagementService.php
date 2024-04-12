<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ClientAccountException;
use App\Models\ClientAccount;
use App\Repositories\ClientAccountRepository;

class ClientAccountManagementService
{
    public function __construct(readonly private ClientAccountRepository $clientAccountRepository)
    {
    }

    /**
     * @throws ClientAccountException
     */
    public function reduceBalance(int $accountId, float $amount): ClientAccount
    {
        $account = $this->clientAccountRepository->getAccountById($accountId);

        if (!$account) {
            throw new ClientAccountException('Account not found!');
        }

        if ($account->balance < $amount) {
            throw new ClientAccountException('Available balance is too low!');
        }

        $account->balance = $account->balance - $amount;

        return $this->clientAccountRepository->store($account);
    }

    /**
     * @throws ClientAccountException
     */
    public function increaseBalance(int $accountId, float $amount): ClientAccount
    {
        $account = $this->clientAccountRepository->getAccountById($accountId);

        if (!$account) {
            throw new ClientAccountException('Account not found!');
        }

        $account->balance = $account->balance + $amount;

        return $this->clientAccountRepository->store($account);
    }
}
