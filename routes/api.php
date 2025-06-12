<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\PageController;

// Public routes
Route::post('/login', [LoginController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
     Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('pages', PageController::class);
    Route::put('/pages/{page}/reorder-sections', [PageController::class, 'reorderSections']);
    Route::get('/pages/layouts', [PageController::class, 'getLayouts']);
    Route::post('/uploads', [PageController::class, 'upload']);

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    });
});

