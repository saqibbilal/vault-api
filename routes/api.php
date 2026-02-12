<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DocumentController;
use Gemini\Laravel\Facades\Gemini;

Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});


Route::prefix('v1')->group(function () {
    // Auth Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('documents', DocumentController::class);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('/search/semantic', [DocumentController::class, 'search']);
    });
});


///**  */Gemini connection test */

//Route::get('/test-ai', function () {
//    try {
//        // Pass the model name as a string directly
//        $result = Gemini::embeddingModel('text-embedding-004')
//            ->embedContent("Testing my vault connection");
//
//        return response()->json([
//            'status' => 'success',
//            'dimensions' => count($result->embedding->values),
//            'first_five_values' => array_slice($result->embedding->values, 0, 5)
//        ]);
//    } catch (\Exception $e) {
//        return response()->json([
//            'status' => 'error',
//            'message' => $e->getMessage()
//        ], 500);
//    }
//});
