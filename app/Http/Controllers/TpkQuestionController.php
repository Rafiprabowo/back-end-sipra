<?php

namespace App\Http\Controllers;

use App\Models\TPKQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TpkQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Mengambil semua data soal
    $soal = TPKQuestion::all();

    // Format data soal sesuai yang diinginkan
    $formattedSoal = $soal->map(function ($soal) {
        return [
            'id' => $soal->id,
            'question_text' => $soal->question_text,
            'question_image' => $soal->question_image, // Menyimpan nama file gambar
            'image_url' => $soal->question_image ? asset('storage/' . $soal->question_image) : null, // Menambahkan URL gambar yang dapat diakses
            'difficulty' => $soal->difficulty,
            'option1' => $soal->options[0]  ?? null, // Ambil opsi pertama
            'option2' => $soal->options[1] ?? null, // Ambil opsi kedua
            'option3' => $soal->options[2] ?? null, // Ambil opsi ketiga
            'option4' => $soal->options[3] ?? null,
            'option5' => $soal->options[4] ?? null,// Ambil opsi keempat
            'is_correct' => $soal->is_correct,
            'created_at' => $soal->created_at->toIso8601String(), // Format tanggal
            'updated_at' => $soal->updated_at->toIso8601String(), // Format tanggal
        ];
    });

    // Return data dalam format yang diinginkan
    return response()->json(['data' => $formattedSoal], 200);
}

    public function getById($id)
{
    // Cari soal berdasarkan ID
    $soal = TPKQuestion::find($id);

    // Jika soal tidak ditemukan, kembalikan error response
    if (!$soal) {
        return response()->json(['message' => 'Soal tidak ditemukan.'], 404);
    }

    // Format data sesuai kebutuhan
    $formattedSoal = [
        'id' => $soal->id,
        'question_text' => $soal->question_text,
        'question_image' => $soal->question_image ? asset('storage/'.$soal->question_image) : null, // Menghasilkan URL gambar
        'difficulty' => $soal->difficulty,
        'options' => $soal->options, // Data sudah dalam bentuk array
        'is_correct' => $soal->is_correct,
        'created_at' => $soal->created_at->toIso8601String(),
        'updated_at' => $soal->updated_at->toIso8601String(),
    ];

    // Kembalikan response dengan data soal
    return response()->json(['data' => $formattedSoal], 200);
}




    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
{
    // Validasi input
    $validatedData = $request->validate([
        'question_text' => 'nullable|string',
        'difficulty' => 'required|in:low,intermediate,advance',
        'opsi1' => 'required|string',
        'opsi2' => 'required|string',
        'opsi3' => 'required|string',
        'opsi4' => 'required|string',
        'opsi5' => 'required|string',
        'is_correct' => 'required|integer|min:0|max:4',
        'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi gambar
    ]);

    // Menyusun array opsi jawaban
    $options = [
        $validatedData['opsi1'],
        $validatedData['opsi2'],
        $validatedData['opsi3'],
        $validatedData['opsi4'],
        $validatedData['opsi5'],
    ];

    // Membuat objek soal baru
    $question = new TPKQuestion();
    $question->question_text = $validatedData['question_text'] ?? null;
    $question->difficulty = $validatedData['difficulty'];
    $question->is_correct = $validatedData['is_correct'];
    $question->options = $options; // Menyimpan opsi dalam bentuk array JSON

    // Jika ada gambar yang diupload, simpan gambar dan perbarui path di database
    if ($request->hasFile('question_image')) {
        // Simpan gambar
        $path = $request->file('question_image')->store('public/questions_images');
        // Menyimpan path relatif gambar ke dalam database
        $question->question_image = $path;
    }

    // Simpan data soal ke database
    $question->save();

    // Kembalikan response sukses dengan URL gambar yang dapat diakses
    return response()->json([
        'message' => 'Question created successfully',
        'data' => [
            'id' => $question->id,
            'question_text' => $question->question_text,
            'question_image' => $question->question_image ? asset('storage/' . $question->question_image) : null, // Menghasilkan URL gambar
            'difficulty' => $question->difficulty,
            'options' => $question->options,
            'is_correct' => $question->is_correct,
            'created_at' => $question->created_at->toIso8601String(),
            'updated_at' => $question->updated_at->toIso8601String(),
        ]
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, $questionId)
{
    // Validasi input
    $validatedData = $request->validate([
        'question_text' => 'nullable|string',
        'difficulty' => 'required|in:low,intermediate,advance',
        'opsi1' => 'required|string',
        'opsi2' => 'required|string',
        'opsi3' => 'required|string',
        'opsi4' => 'required|string',
        'opsi5' => 'required|string',
        'is_correct' => 'required|integer|min:0|max:4',
        'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validasi gambar
    ]);

    // Cari soal berdasarkan ID
    $question = TPKQuestion::find($questionId);
    if (!$question) {
        return response()->json(['message' => 'Question not found'], 404);
    }

    // Menyusun array opsi jawaban
    $options = [
        $validatedData['opsi1'],
        $validatedData['opsi2'],
        $validatedData['opsi3'],
        $validatedData['opsi4'],
        $validatedData['opsi5'],
    ];

    // Perbarui data soal
    $question->question_text = $validatedData['question_text'] ?? $question->question_text;
    $question->difficulty = $validatedData['difficulty'];
    $question->is_correct = $validatedData['is_correct'];
    $question->options = $options; // Menyimpan opsi dalam bentuk array JSON

    // Jika ada gambar yang diupload, simpan gambar dan perbarui path di database
    if ($request->hasFile('question_image')) {
        // Hapus gambar lama jika ada
        if ($question->question_image && Storage::exists($question->question_image)) {
            Storage::delete($question->question_image);
        }

        // Simpan gambar baru
        $path = $request->file('question_image')->store('public/questions_images');
        $question->question_image = $path;
    }

    // Simpan perubahan ke database
    $question->save();

    // Format data soal untuk response
    $formattedQuestion = [
        'id' => $question->id,
        'question_text' => $question->question_text,
        'question_image' => $question->question_image ? asset('storage/' . $question->question_image) : null, // Menghasilkan URL gambar
        'difficulty' => $question->difficulty,
        'options' => $question->options,
        'is_correct' => $question->is_correct,
        'created_at' => $question->created_at->toIso8601String(),
        'updated_at' => $question->updated_at->toIso8601String(),
    ];

    // Kembalikan response sukses dengan data yang diperbarui
    return response()->json([
        'message' => 'Question updated successfully',
        'data' => $formattedQuestion,
    ], 200);
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
