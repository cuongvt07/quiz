<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionChoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
 */
class AnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $choice = QuestionChoice::inRandomOrder()->first() ?? QuestionChoice::factory()->create();

        return [
            'question_id'        => $choice->question_id,
            'question_choice_id' => $choice->id,
            'user_id'            => User::inRandomOrder()->value('id') ?? User::factory(),
            'score'              => rand(0, 100),
            'category'           => $this->faker->word(),
        ];
    }
}
