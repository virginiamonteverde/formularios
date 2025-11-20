<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicFormController;

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
    return view('welcome');
});

// Ver un formulario público por su slug
Route::get('/f/{slug}', [PublicFormController::class, 'show'])
    ->name('forms.public.show');

// Enviar respuestas de un formulario público
Route::post('/f/{slug}', [PublicFormController::class, 'submit'])
    ->name('forms.public.submit');
