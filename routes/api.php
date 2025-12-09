<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramAIController;

// Route webhook Telegram
Route::post('/api/telegram/webhook', [TelegramAIController::class, 'handle']);

// Route setup webhook
Route::get('/telegram/setup-webhook', [TelegramAIController::class, 'setWebhook']);


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|   
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

