<?php

use App\Http\Controllers\PDFExporterController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (User::isAdmin())
        return redirect('/admin');
    else
        return redirect('/dosen');
});


Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');


Route::get('/presensi/{user:id}/export-pelanggaran', [PDFExporterController::class, 'exportPelanggaran'])->name('pdf-export.pelanggaran');
