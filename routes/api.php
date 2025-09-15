<?php

use App\Http\Controllers\Api\AuditController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Public category routes
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'show']);
});

// Protected routes (JWT authentication required)
Route::middleware(['auth:api'])->prefix('v1/admin')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });

    // Order routes (customer permissions)
    Route::prefix('orders')->middleware(['permission:view orders'])->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store'])->middleware(['permission:create orders']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}', [OrderController::class, 'update'])->middleware(['permission:edit orders']);
    });

    // Image management routes
    Route::prefix('images')->middleware(['permission:view products'])->group(function () {
        Route::get('/', [ImageController::class, 'index']);
        Route::post('/', [ImageController::class, 'store'])->middleware(['permission:create products']);
        Route::get('/{id}', [ImageController::class, 'show']);
        Route::put('/{id}', [ImageController::class, 'update'])->middleware(['permission:edit products']);
        Route::delete('/{id}', [ImageController::class, 'destroy'])->middleware(['permission:delete products']);
        Route::post('/attach', [ImageController::class, 'attach'])->middleware(['permission:edit products']);
        Route::post('/detach', [ImageController::class, 'detach'])->middleware(['permission:edit products']);
    });

    // Audit routes (admin only)
    Route::prefix('audits')->middleware(['auth:api', 'permission:view audits'])->group(function () {
        Route::get('/', [AuditController::class, 'index']);
        Route::get('/statistics', [AuditController::class, 'statistics']);
        Route::get('/recent', [AuditController::class, 'recent']);
        Route::get('/model/{modelType}/{modelId}', [AuditController::class, 'modelAudits']);
        Route::get('/{id}', [AuditController::class, 'show']);
        Route::delete('/clean', [AuditController::class, 'clean'])->middleware(['permission:delete audits']);
    });

    // Admin/Manager routes
    Route::middleware(['permission:view products'])->group(function () {
        // Product management routes
        Route::prefix('products')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::post('/', [ProductController::class, 'store'])->middleware(['permission:create products']);
            Route::put('/{id}', [ProductController::class, 'update'])->middleware(['permission:edit products']);
            Route::delete('/{id}', [ProductController::class, 'destroy'])->middleware(['permission:delete products']);
        });

        // Category management routes
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::post('/', [CategoryController::class, 'store'])->middleware(['permission:create categories']);
            Route::put('/{id}', [CategoryController::class, 'update'])->middleware(['permission:edit categories']);
            Route::delete('/{id}', [CategoryController::class, 'destroy'])->middleware(['permission:delete categories']);
        });
    });

    // Example protected route
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'This is a protected route',
            'data' => [
                'user' => $request->user(),
                'roles' => $request->user()->getRoleNames(),
                'permissions' => $request->user()->getAllPermissions()->pluck('name'),
            ],
        ]);
    });
});