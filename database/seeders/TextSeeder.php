<?php

namespace Database\Seeders;

use App\Models\Text;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Text::factory()->times(10)->create();
        $texts = [
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'What worries you, masters you.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'Little by little, one travels far.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'The merit of all things lies in their difficulty.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'Discipline is the bridge between goals and accomplishment.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'Quiet people have the loudest minds.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'Nothing can bring you peace but yourself.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'Some people feel the rain. Others just get wet.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'The pessimist sees difficulty in every opportunity. The optimist sees opportunity in every difficulty.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'You will not do incredible things without an incredible dream.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'Never regret your past. Rather, embrace it as the teacher that it is.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'Acknowledging the good that you already have in your life is the foundation for all abundance.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'I find the best way to love someone is not to change them, but instead, help them reveal the greatest version of themselves.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'Have a heart that never hardens, and a temper that never tires, and a touch that never hurts.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'If you set your goals ridiculously high and its a failure, you will fail above everyone elses success.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'What lies behind us and what lies before us are tiny matters compared to what lies within us.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'We can either add to our character each day, or we can fritter away our energies in distractions.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'A person without a sense of humor is like a wagon without springs, jolted by every pebble in the road.',
                'type_id' => ''
            ],
            [
                'cefr_id' => '',
                'difficulty' => '',
                'id' => Str::uuid(),
                'source_id' => '',
                'text' => 'Awaken. Take heed, do not squander your life.',
                'type_id' => ''
            ],
        ];
    }
}