<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TpkQuestionController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/questions/tpk', [TpkQuestionController::class, 'index']);
Route::post('/questions/tpk', [TpkQuestionController::class, 'store']);
Route::post('/questions/tpk/{id}', [TpkQuestionController::class, 'update']);
Route::get('/questions/tpk/{id}', [TpkQuestionController::class, 'getById']);
