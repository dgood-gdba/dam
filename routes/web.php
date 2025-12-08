<?php

use App\Livewire\Share;
use App\Models\Asset;
use Illuminate\Support\Facades\Route;

Route::get('/preview/{asset}', static function (Asset $asset) {
    if (!Storage::disk('private')->exists($asset->path)) {
        abort(405);
    }
    $path = Storage::disk('private')->path($asset->path);
    $mimeType = Storage::disk('private')->mimeType($asset) ?? 'application/octet-stream';

    return response()->stream(function () use ($asset) {
        $stream = Storage::disk('private')->readStream($asset->path);
        fpassthru($stream);
        if (is_resource($stream)) {
            fclose($stream);
        }
    }, 200, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
    ]);
})
    ->where('path', '.*')
    ->name('files.preview')
    ->middleware('signed');


Route::get('/share/{asset}', Share::class)
    ->where('path', '.*')
    ->name('share.asset')
    ->middleware('signed');
