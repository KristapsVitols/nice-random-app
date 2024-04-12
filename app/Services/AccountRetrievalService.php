<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ClientAccount;
use App\Repositories\ClientAccountRepository;
use App\Exceptions\ClientAccountException;

readonly class AccountRetrievalService
{
    public function __construct(private ClientAccountRepository $clientAccountRepository)
    {
    }

    /**
     * @throws ClientAccountException
     */
    public function getAccountsByClientId(int $clientId): array
    {
        if (!$clientId) {
            throw new ClientAccountException('Please provide a client id!');
        }

        return array_map(
            fn(ClientAccount $account) => $account->toApiData(),
            $this->clientAccountRepository->getAccountsByClientId($clientId)
        );
    }
}
