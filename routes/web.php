<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\FoodDiaryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScannerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NutriScan AI - Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Publik (Landing Page)
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Autentikasi Pengguna
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Halaman Terproteksi (Hanya Pengguna Terautentikasi)
Route::middleware('auth')->group(function () {
    // 1. Dashboard Utama NutriScan AI
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Database Makanan & Gizi (Full CRUD: Master Data Nutrisi)
    Route::resource('foods', FoodController::class);
    Route::post('/foods/{food}/quick-log', [FoodController::class, 'quickLog'])->name('foods.quick-log');

    // 3. AI Food Scanner
    Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index');
    Route::post('/scanner', [ScannerController::class, 'analyze'])->name('scanner.analyze');
    Route::get('/scan/{id}', [ScannerController::class, 'show'])->name('scanner.result');
    Route::post('/scan/{id}/save', [ScannerController::class, 'saveToDiary'])->name('scanner.save');

    // 4. Food Diary (Jurnal Makan Harian Full CRUD)
    Route::get('/food-diary', [FoodDiaryController::class, 'index'])->name('diary.index');
    Route::post('/food-diary', [FoodDiaryController::class, 'store'])->name('diary.store');
    Route::get('/food-diary/{id}/edit', [FoodDiaryController::class, 'edit'])->name('diary.edit');
    Route::put('/food-diary/{id}', [FoodDiaryController::class, 'update'])->name('diary.update');
    Route::delete('/food-diary/{id}', [FoodDiaryController::class, 'destroy'])->name('diary.destroy');

    // 5. Riwayat Nutrisi & Tren Analisis
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

    // 6. Asisten Gizi AI (Chatbot Interaktif)
    Route::get('/assistant', [AssistantController::class, 'index'])->name('assistant.index');
    Route::post('/assistant/chat', [AssistantController::class, 'chat'])->name('assistant.chat');

    // 7. Profil & Pengaturan Target Nutrisi
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Route awal praktikum
Route::get('/awal', function () {
    return view('awal');
})->name('praktikum.awal');