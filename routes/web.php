<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CriteriaController;
use App\Http\Controllers\FormulasController;
use App\Http\Controllers\GraphController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManagementUnitController;
use App\Http\Controllers\ManagementUserController;
use App\Http\Controllers\PerformanceUnitController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SubCriteriaController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitDetailController;
use App\Http\Controllers\WeightController;
use App\Models\PerformanceUnit;
use App\Models\Questions;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth
Route::get('login', [LoginController::class, 'view']);
Route::post('login', [LoginController::class, 'authenticate']);

// Achievement
Route::get('/capaian', [AchievementController::class, 'index'])->name('achievement.index');
Route::post('/capaian', [AchievementController::class, 'save'])->name('achievement.save');

// Criteria
Route::get('/criteria', [CriteriaController::class, 'index'])->name('criteria.index');
Route::post('/criteria/{id?}', [CriteriaController::class, 'store'])->name('criteria.store');
Route::delete('/criteria/{id}', [CriteriaController::class, 'destroy'])->name('criteria.destroy');

// Target
Route::get('/target', [TargetController::class, 'index'])->name('target.index');
Route::post('/target', [TargetController::class, 'save'])->name('target.save');

// Score
Route::get('/skor', [FormulasController::class, 'index'])->name('skor.index');
Route::get('/generate', [FormulasController::class, 'generate'])->name('formula.generate');

// Graph
Route::get('/grafik', [GraphController::class, 'index'])->name('grafik.index');
Route::get('/get-grafik-data', [GraphController::class, 'getChartData']);

// Management Pertanyaan
Route::get('/data-pertanyaan', [QuestionController::class, 'index'])->name('questions.index');
Route::get('/pertanyaan/{id?}', [QuestionController::class, 'edit'])->name('questions.edit');
Route::post('/pertanyaan/{id?}', [QuestionController::class, 'store'])->name('questions.store');
Route::delete('/pertanyaan/{id}', [QuestionController::class, 'destroy'])->name('question.destroy');
Route::get('/storeDumpQuestion', [QuestionController::class, 'storeDumpData']);

Route::get('/storeDumpWeight', [WeightController::class, 'storeDataDump']);
Route::get('/bobot', [WeightController::class, 'index'])->name('question-weight.index');
Route::post('/bobot/{id?}', [WeightController::class, 'store'])->name('question-weight.store');
Route::delete('/bobot/{id}', [WeightController::class, 'destroy'])->name('question-weight.destroy');

// Management Unit
Route::get('/manajemen-unit', [ManagementUnitController::class, 'index'])->name('management-unit.index');
Route::post('/manajemen-unit/{id?}', [ManagementUnitController::class, 'store'])->name('management-unit.store');
Route::delete('/manajemen-unit/{id}', [ManagementUnitController::class, 'destroy'])->name('management-unit.destroy');

// Management User
Route::get('/manajemen-pengguna', [ManagementUserController::class, 'index'])->name('management-user.index');
Route::post('/manajemen-pengguna', [ManagementUserController::class, 'create'])->name('management-user.create');
Route::put('/manajemen-pengguna/{id}', [ManagementUserController::class, 'update'])->name('management-user.update');
Route::delete('/manajemen-pengguna/{id}', [ManagementUserController::class, 'destroy'])->name('management-user.destroy');


// Sub Criteria
Route::get('/sub-kriteria', [SubCriteriaController::class, 'index'])->name('sub-criteria.index');
Route::post('/sub-kriteria/{id?}', [SubCriteriaController::class, 'store'])->name('sub-criteria.store');
Route::delete('/sub-kriteria/{id}', [SubCriteriaController::class, 'destroy'])->name('sub-criteria.destroy');

// Unit
Route::get('/units/{id?}', [UnitController::class, 'view']);
Route::post('/units', [UnitController::class, 'create']);
Route::put('/units/{id}', [UnitController::class, 'update']);
Route::delete('/units/{id}', [UnitController::class, 'remove']);

// Unit Detail
Route::get('/unit/{unit_id}/details/{id?}', [UnitDetailController::class, 'view'])->name('unit-detail.view');
Route::post('/unit/{unit_id}/details', [UnitDetailController::class, 'create']);
Route::put('/unit/{unit_id}/details/{id}', [UnitDetailController::class, 'update']);
Route::delete('/unit/{unit_id}/details/{id}', [UnitDetailController::class, 'remove']);

Route::get('performance-unit', [PerformanceUnitController::class, 'index'])->name('performance-unit.index');
Route::post('performance-unit', [PerformanceUnitController::class, 'create'])->name('performance-unit.create');
Route::put('performance-unit/{id}', [PerformanceUnitController::class, 'update'])->name('performance-unit.update');
Route::delete('performance-unit/{id}', [PerformanceUnitController::class, 'destroy'])->name('performance-unit.delete');

// Question

