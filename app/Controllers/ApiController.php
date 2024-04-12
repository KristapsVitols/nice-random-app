<?php

namespace App\Controllers;

use App\Exceptions\AccountTransactionException;
use App\Exceptions\ClientAccountException;
use App\Exceptions\TransferFundsException;
use App\Services\AccountRetrievalService;
use App\Services\AccountTransactionRetrievalService;
use App\Services\TransferFundsService;
use Illuminate\Http\JsonResponse;
use Throwable;

readonly class ApiController
{
    public function __construct(
        private AccountRetrievalService            $accountRetrievalService,
        private AccountTransactionRetrievalService $accountTransactionRetrievalService,
        private TransferFundsService $fundsService,
    )
    {
    }

    public function getClientAccounts($clientId): JsonResponse
    {
        try {
            return response()->json($this->accountRetrievalService->getAccountsByClientId((int)$clientId));
        } catch (ClientAccountException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Something went unexpectedly wrong!'], 500);
        }
    }

    public function getAccountTransactions($accountId): JsonResponse
    {
        try {
            return response()->json(
                $this->accountTransactionRetrievalService->getTransactionsByAccountId(
                    (int)$accountId,
                    (int)request()->query('offset'),
                    (int)request()->query('limit'),
                )
            );
        } catch (AccountTransactionException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Something went unexpectedly wrong!'], 500);
        }
    }

    public function transferFunds(): JsonResponse
    {
        try {
            return response()->json(
                $this->fundsService->attemptTransfer(
                    (int)request()->post('accountIdFrom'),
                    (int)request()->post('accountIdTo'),
                    (float)request()->post('amount'),
                    (string)request()->post('currency'),
                )
            );
        } catch (TransferFundsException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Something went unexpectedly wrong!'], 500);
        }
    }
}
