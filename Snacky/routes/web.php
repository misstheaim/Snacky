<?php

use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/pdf/{receipt}', function (Receipt $receipt) {
    $pdf = Pdf::loadView('receipt-pdf', ['receipt' =>$receipt]);

    return $pdf->download($receipt->title . '-' . $receipt->date . '.pdf');
})->name('pdf');

Route::view('/test', 'receipt-pdf', ['receipt' => Receipt::find(8)]);

Route::fallback(function () {
    return redirect('/admin');
});