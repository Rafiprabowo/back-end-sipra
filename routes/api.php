<?php

use App\Imports\TpkQuestionImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TpkQuestionController;
use App\Models\TPKQuestion;
use Maatwebsite\Excel\Facades\Excel;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/questions/tpk', [TpkQuestionController::class, 'index']);
Route::post('/questions/tpk', [TpkQuestionController::class, 'store']);
Route::post('/questions/tpk/{id}', [TpkQuestionController::class, 'update']);
Route::get('/questions/tpk/{id}', [TpkQuestionController::class, 'getById']);
Route::delete('/questions/tpk/{id}', [TpkQuestionController::class, 'destroy']);

Route::post('/file-excel', function (Request $request) {
   // Validasi file
   $request->validate([
        'file' => 'required|mimes:xlsx,xls',
   ]);

   try {
       // Ambil file yang di-upload
       $file = $request->file('file');
       
       // Import data dari file Excel
       Excel::import(new TpkQuestionImport, $file);

       // Ambil data yang sudah diimpor
       $questions = TPKQuestion::all();

       // Response sukses
       return response()->json([
           'status' => 'success',
           'message' => 'File berhasil diproses dan data disimpan.',
           'file_info' => [
               'file_name' => $file->getClientOriginalName(),
               'file_size' => $file->getSize(),
           ],
           'data' => $questions, // Bisa mengembalikan data yang sudah disimpan
       ], 200); // Status HTTP 200 (OK)

   } catch (\Exception $e) {
       // Tangani error jika ada
       return response()->json([
           'status' => 'error',
           'message' => 'Terjadi kesalahan saat memproses file.',
           'error' => $e->getMessage(), // Pesan error yang lebih rinci
       ], 500); // Status HTTP 500 (Internal Server Error)
   }
});


