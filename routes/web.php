<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('send-mail', function () {

    $details = [
        'title' => 'Mail from ItSolutionStuff.com',
        'body' => 'This is for testing email using smtp'
    ];

    \Mail::to('your_receiver_email@gmail.com')->send(new \App\Mail\MyTestMail($details));

    dd("Email is Sent.");
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('test1', function () {
    // Storage::disk('google')->put('test.txt', 'Hello world');
    $filePath = public_path('images\logo.jpg');
    $fileData = File::get($filePath);
    Storage::disk('google')->put('test.jpg', $fileData);
});
