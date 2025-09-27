<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\QuestionChoice;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $question = Question::create([
            'content' => $row['content'],
        ]);

        foreach (['choice_a', 'choice_b', 'choice_c', 'choice_d'] as $key => $choiceKey) {
            QuestionChoice::create([
                'question_id' => $question->id,
                'content' => $row[$choiceKey],
                'is_correct' => $row['correct_choice'] == $key,
            ]);
        }

        return $question;
    }
}