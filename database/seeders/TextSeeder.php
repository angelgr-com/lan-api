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
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'A1')->value('id'),
                'difficulty' => 'easy',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Locke')->value('id'),
                'text' => 'What worries you, masters you.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'A1')->value('id'),
                'difficulty' => 'easy',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Tolkien')->value('id'),
                'text' => 'Little by little, one travels far.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'A1')->value('id'),
                'difficulty' => 'easy',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Dumas')->value('id'),
                'text' => 'The merit of all things lies in their difficulty.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'A2')->value('id'),
                'difficulty' => 'easy',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Rohn')->value('id'),
                'text' => 'Discipline is the bridge between goals and accomplishment.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'A2')->value('id'),
                'difficulty' => 'easy',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io King')->value('id'),
                'text' => 'Quiet people have the loudest minds.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'A2')->value('id'),
                'difficulty' => 'easy',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Carnegie')->value('id'),
                'text' => 'Nothing can bring you peace but yourself.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'B1')->value('id'),
                'difficulty' => 'medium',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Dylan')->value('id'),
                'text' => 'Some people feel the rain. Others just get wet.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'B1')->value('id'),
                'difficulty' => 'medium',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Churchill')->value('id'),
                'text' => 'The pessimist sees difficulty in every opportunity. The optimist sees opportunity in every difficulty.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'B1')->value('id'),
                'difficulty' => 'medium',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Eliot')->value('id'),
                'text' => 'You will not do incredible things without an incredible dream.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'B2')->value('id'),
                'difficulty' => 'medium',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Sharma')->value('id'),
                'text' => 'Never regret your past. Rather, embrace it as the teacher that it is.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'B2')->value('id'),
                'difficulty' => 'medium',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Tolle')->value('id'),
                'text' => 'Acknowledging the good that you already have in your life is the foundation for all abundance.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'B2')->value('id'),
                'difficulty' => 'medium',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Maraboli')->value('id'),
                'text' => 'I find the best way to love someone is not to change them, but instead, help them reveal the greatest version of themselves.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'C1')->value('id'),
                'difficulty' => 'hard',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Dickens')->value('id'),
                'text' => 'Have a heart that never hardens, and a temper that never tires, and a touch that never hurts.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'C1')->value('id'),
                'difficulty' => 'hard',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Cameron')->value('id'),
                'text' => 'If you set your goals ridiculously high and its a failure, you will fail above everyone elses success.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'C1')->value('id'),
                'difficulty' => 'hard',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Emerson')->value('id'),
                'text' => 'What lies behind us and what lies before us are tiny matters compared to what lies within us.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'C2')->value('id'),
                'difficulty' => 'hard',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Deng')->value('id'),
                'text' => 'We can either add to our character each day, or we can fritter away our energies in distractions.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'C2')->value('id'),
                'difficulty' => 'hard',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Beecher')->value('id'),
                'text' => 'A person without a sense of humor is like a wagon without springs, jolted by every pebble in the road.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
            [
                'cefr_id' => DB::table('cefrs')->where('level', '=', 'C2')->value('id'),
                'difficulty' => 'hard',
                'id' => Str::uuid(),
                'source_id' => DB::table('sources')->where('url', '=', 'zenquotes.io Dogen')->value('id'),
                'text' => 'Awaken. Take heed, do not squander your life.',
                'type_id' => DB::table('types')->where('type', '=', 'quote')->value('id'),
            ],
        ];

        DB::table('texts')->insert($texts);
    }
}