<?php

use App\Http\Controllers\Admin\AromaChordController;
use App\Http\Controllers\Admin\AromaGroupController;
use App\Http\Controllers\Admin\AromaNoteController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\NoteGroupingController;
use App\Http\Controllers\Admin\PerfumerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductSubtypeController;
use App\Http\Controllers\Admin\ProductSubtypeFormatController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Middleware\AdminMiddleware;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AdminAuthenticatedSessionController::class, 'store']);
    });


    Route::middleware([AdminMiddleware::class])->group(function () {
        Route::post('/upload', [UploadController::class, 'upload'])->name('upload');

        Route::get('/', function () {
            return redirect(route('admin.products.index'));
        })->name('home');

        Route::resource('countries', CountryController::class)
            ->only(['index', 'store', 'update', 'destroy', 'show']);

        Route::resource('brands', BrandController::class)
            ->only(['index', 'store', 'update', 'destroy', 'show']);

        Route::resource('perfumers', PerfumerController::class)
            ->only(['index', 'store', 'update', 'destroy', 'show']);

        Route::resource('aroma-groups', AromaGroupController::class)
            ->only(['index', 'store', 'update', 'destroy','show']);

        Route::resource('aroma-chords', AromaChordController::class)
            ->only(['index', 'store', 'update', 'destroy', 'show']);

        Route::resource('aroma-notes', AromaNoteController::class)
            ->only(['index', 'store', 'update', 'destroy', 'show']);

        Route::resource('note-groupings', NoteGroupingController::class)
            ->only(['store', 'update', 'destroy', 'show']);

        Route::resource('product-subtype-formats', ProductSubtypeFormatController::class)
            ->only(['index', 'store', 'update', 'destroy', 'show']);

        Route::resource('products', ProductController::class)
            ->only(['index', 'store', 'update', 'destroy', 'edit']);

        Route::get('/products/filter', [ProductController::class, 'filter'])->name('products.filter');

        Route::resource('product-subtypes', ProductSubtypeController::class)
            ->only(['index', 'store', 'update', 'destroy', 'show']);

        Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });
});
