<?php

use Illuminate\Support\Facades\Route;

// Swagger UI only (docs remain accessible)
Route::get('/docs', function () {
    return view('swagger');
});

Route::get('/docs/openapi.yaml', function () {
    $path = base_path('docs/openapi.yaml');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, ['Content-Type' => 'application/yaml']);
});
