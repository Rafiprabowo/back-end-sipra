<?php

namespace App\Imports;

use App\Models\TPKQuestion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TpkQuestionImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new TPKQuestion([
            //
            'question_text' => $row['pertanyaan'],
            'difficulty' => $row['tingkat_kesulitan'],
            'options' => [$row['opsi_1'],$row['opsi_2'],$row['opsi_3'],$row['opsi_4'],$row['opsi_5']],
            'is_correct' => ($row['kunci_jawaban'] - 1),
            'question_image' => null
        ]);
    }
}
