<?php

namespace Tests\Unit;

use App\Exceptions\TransferFundsException;
use App\Helpers\DBHelper;
use App\Models\AccountTransaction;
use App\Models\ClientAccount;
use App\Repositories\ClientAccountRepository;
use App\Services\AccountTransactionManagementService;
use App\Services\ClientAccountManagementService;
use App\Services\CurrencyConversionService;
use App\Services\TransferFundsService;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

class TransferFundsServiceTest extends TestCase
{
    private ClientAccountRepository|MockInterface $mockedClientAccountRepository;
    private CurrencyConversionService|MockInterface $mockedConversionService;
    private AccountTransactionManagementService|MockInterface $mockedAccountTransactionManagementService;
    private ClientAccountManagementService|MockInterface $mockedAccountManagementService;
    private DBHelper|MockInterface $mockedDb;
    private TransferFundsService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockedClientAccountRepository = Mockery::mock(ClientAccountRepository::class);
        $this->mockedConversionService = Mockery::mock(CurrencyConversionService::class);
        $this->mockedAccountTransactionManagementService = Mockery::mock(AccountTransactionManagementService::class);
        $this->mockedAccountManagementService = Mockery::mock(ClientAccountManagementService::class);
        $this->mockedDb = Mockery::mock(DBHelper::class);

        $this->service = new TransferFundsService(
            $this->mockedClientAccountRepository,
            $this->mockedConversionService,
            $this->mockedAccountTransactionManagementService,
            $this->mockedAccountManagementService,
            $this->mockedDb,
        );
    }

    public function testAttemptTransferWithAccountsMissingThrowsException()
    {
        $this->mockedClientAccountRepository->shouldReceive('getAccountById')->twice()->andReturnNull();

        self::expectException(TransferFundsException::class);
        self::expectExceptionMessage('Please provide valid accounts!');

        $this->service->attemptTransfer(1, 2, 123, 'USD');
    }

    public function testAttemptTransferWithInvalidCurrencyThrowsException()
    {
        $accountFrom = new ClientAccount();
        $accountFrom->currency = 'USD';

        $accountTo = new ClientAccount();
        $accountTo->currency = 'EUR';

        $this->mockedClientAccountRepository->shouldReceive('getAccountById')->andReturn($accountFrom);
        $this->mockedClientAccountRepository->shouldReceive('getAccountById')->andReturn($accountTo);

        self::expectException(TransferFundsException::class);
        self::expectExceptionMessage('Invalid currency provided!');

        $this->service->attemptTransfer(1, 2, 444, 'GBP');
    }

    public function testAttemptTransferWithInvalidBalanceWithoutConversionThrowsException()
    {
        $accountFrom = new ClientAccount();
        $accountFrom->currency = 'USD';
        $accountFrom->balance = 100;

        $accountTo = new ClientAccount();
        $accountTo->currency = 'EUR';

        $this->mockedClientAccountRepository->shouldReceive('getAccountById')->andReturn($accountFrom);
        $this->mockedClientAccountRepository->shouldReceive('getAccountById')->andReturn($accountTo);

        self::expectException(TransferFundsException::class);
        self::expectExceptionMessage('Not enough funds available for the transfer!');

        $this->service->attemptTransfer(1, 2, 101, 'USD');
    }

    public function testAttemptTransferWithInvalidBalanceWithConversionThrowsException()
    {
        $accountFrom = new ClientAccount();
        $accountFrom->currency = 'USD';
        $accountFrom->balance = 100;

        $accountTo = new ClientAccount();
        $accountTo->currency = 'EUR';

        $this->mockedClientAccountRepository->shouldReceive('getAccountById')->once()->andReturn($accountFrom);
        $this->mockedClientAccountRepository->shouldReceive('getAccountById')->once()->andReturn($accountTo);
        $this->mockedConversionService->shouldReceive('convert')->andReturn(101);

        self::expectException(TransferFundsException::class);
        self::expectExceptionMessage('Not enough funds available for the transfer!');

        $this->service->attemptTransfer(1, 2, 99, 'EUR');
    }

    public function testAttemptTransferWithValidInputsSucceeds()
    {
        $accountFrom = new ClientAccount();
        $accountFrom->id = 1;
        $accountFrom->currency = 'USD';
        $accountFrom->balance = 100;

        $accountTo = new ClientAccount();
        $accountTo->id = 2;
        $accountTo->currency = 'EUR';

        $expectedTransaction = new AccountTransaction();
        $expectedTransaction->transaction_amount = 50;
        $expectedTransaction->transaction_currency = 'USD';

        $this->mockedClientAccountRepository->shouldReceive('getAccountById')->once()->andReturn($accountFrom);
        $this->mockedClientAccountRepository->shouldReceive('getAccountById')->once()->andReturn($accountTo);
        $this->mockedDb->shouldReceive('beginTransaction');
        $this->mockedConversionService->shouldReceive('convert')->andReturn(52.5);
        $this->mockedAccountManagementService->shouldReceive('reduceBalance')->once();
        $this->mockedAccountManagementService->shouldReceive('increaseBalance')->once();
        $this->mockedAccountTransactionManagementService->shouldReceive('createTransaction')->once()->andReturn($expectedTransaction);
        $this->mockedDb->shouldReceive('commit')->once();

        $transaction = $this->service->attemptTransfer(1, 2, 50, 'USD');

        self::assertEquals($expectedTransaction->transaction_amount, $transaction->transaction_amount);
    }
}
