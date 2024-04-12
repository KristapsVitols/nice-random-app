<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CurrencyConversionException;
use App\Exceptions\TransferFundsException;
use App\Helpers\DBHelper;
use App\Models\AccountTransaction;
use App\Models\ClientAccount;
use App\Repositories\ClientAccountRepository;
use Throwable;

readonly class TransferFundsService
{
    public function __construct(
        private ClientAccountRepository   $clientAccountRepository,
        private CurrencyConversionService $conversionService,
        private AccountTransactionManagementService $accountTransactionManagementService,
        private ClientAccountManagementService $accountManagementService,
        private DBHelper $db,
    )
    {
    }

    /**
     * @throws TransferFundsException
     * @throws CurrencyConversionException
     */
    public function attemptTransfer(int $accountIdFrom, int $accountIdTo, float $amount, string $currency): AccountTransaction
    {
        $accountFrom = $this->clientAccountRepository->getAccountById($accountIdFrom);
        $accountTo = $this->clientAccountRepository->getAccountById($accountIdTo);

        $this->validateOrFail($accountFrom, $accountTo, $amount, $currency);

        return $this->transferFunds($accountFrom, $accountTo, $amount, $currency);
    }

    /**
     * @throws TransferFundsException
     * @throws CurrencyConversionException
     */
    private function validateOrFail(?ClientAccount $accountFrom, ?ClientAccount $accountTo, float $amount, string $currency): void
    {
        if (!$accountFrom || !$accountTo) {
            throw new TransferFundsException('Please provide valid accounts!');
        }

        if (!in_array($currency, [$accountFrom->currency, $accountTo->currency])) {
            throw new TransferFundsException('Invalid currency provided!');
        }

        // Transferring same currency
        if ($currency === $accountFrom->currency && $accountFrom->balance < $amount) {
            throw new TransferFundsException('Not enough funds available for the transfer!');
        }

        if ($currency === $accountFrom->currency) {
            return;
        }

        // Currencies differ, need to validate if there is enough balance
        $convertedAmount = $this->conversionService->convert($currency, $accountFrom->currency, $amount);
        if ($convertedAmount > $accountFrom->balance) {
            throw new TransferFundsException('Not enough funds available for the transfer!');
        }
    }

    /**
     * @throws TransferFundsException
     */
    private function transferFunds(ClientAccount $accountFrom, ClientAccount $accountTo, float $amount, string $currency): AccountTransaction
    {
        $this->db->beginTransaction();

        try {
            $amount = round($amount, 2);

            $currencyFrom = $currency === $accountFrom->currency ? $currency : $accountTo->currency;
            $currencyTo = $currency === $accountTo->currency ? $accountFrom->currency : $currency;

            $convertedAmount = $this->conversionService->convert($currencyFrom, $currencyTo, $amount);

            $amountToReduce = $currency === $accountFrom->currency ? $amount : $convertedAmount;
            $amountToIncrease = $currency === $accountTo->currency ? $amount : $convertedAmount;

            $this->accountManagementService->reduceBalance($accountFrom->id, $amountToReduce);
            $this->accountManagementService->increaseBalance($accountTo->id, $amountToIncrease);

            $transaction = $this->accountTransactionManagementService->createTransaction(
                $accountFrom->id,
                AccountTransaction::TRANSACTION_TYPE_TRANSFER,
                $amount,
                $currency,
                $accountTo->id,
            );

            $this->db->commit();

            return $transaction;
        } catch (Throwable $e) {
            report($e);
            $this->db->rollBack();

            throw new TransferFundsException('Failed to transfer funds!');
        }
    }
}
