<?php

use App\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/clients/{clientId}/accounts', [ApiController::class, 'getClientAccounts']);
Route::get('/accounts/{accountId}/transactions', [ApiController::class, 'getAccountTransactions']);
Route::post('/transfer-funds', [ApiController::class, 'transferFunds']);
