<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ClientAccount;

class ClientAccountRepository
{
    /**
     * @param int $clientId
     * @return ClientAccount[]|array
     */
    public function getAccountsByClientId(int $clientId): array
    {
        return ClientAccount::where(['client_id' => $clientId])->get()->all();
    }

    public function getAccountById(int $accountId): ?ClientAccount
    {
        return ClientAccount::find($accountId);
    }

    public function store(ClientAccount $account): ClientAccount
    {
        $account->save();

        return $account;
    }
}
