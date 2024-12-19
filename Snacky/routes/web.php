<?php

use App\Http\Middleware\EnsureIsAdmin;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect('/admin');
})->name('home');

Route::get('/pdf/{receipt}', function (Receipt $receipt) {
    $pdf = Pdf::loadView('receipt-pdf', ['receipt' => $receipt]);

    return $pdf->download($receipt->title . '-' . $receipt->date . '.pdf');
})->name('pdf');

Route::middleware(EnsureIsAdmin::class)->group(function () {
    Route::redirect('/toTheTelescope', '/telescope')->name('redirectToTelescope');
    Route::redirect('/toTheSentry', 'https://snacky.sentry.io/')->name('redirectToSentry');
    Route::redirect('/redirectToLogViewer', '/log-viewer')->name('redirectToLogViewer');
    Route::get('/receiveCategories', function () {
        $exitcode = Artisan::call('receive-categories');
        if ($exitcode === 0) {
            return response("success", 200);
        } else {
            abort(500);
        }
    })->name('receiveCategories');
});

Route::fallback(function () {
    return redirect('/admin');
});

Route::get('/ddd', function () {
    $heroImage = Storage::disk('local')->get('dolphin.png');
    $uploadedPath = Storage::disk('s3')->put('dolphin.png', $heroImage);
});
