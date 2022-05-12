<?php

namespace Database\Seeders;

use App\Models\Estext;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EstextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Estext::factory()->times(10)->create();
        $estexts = [
            [
                'id' => Str::uuid(),
                'text' => 'Lo que te preocupa, te domina.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'What worries you, masters you.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Poco a poco, uno viaja lejos.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'Little by little, one travels far.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'El mérito de todas las cosas reside en su dificultad.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'The merit of all things lies in their difficulty.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'La disciplina es el puente entre los objetivos y los logros.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'Discipline is the bridge between goals and accomplishment.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Las personas silenciosas tienen las mentes más ruidosas.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'Quiet people have the loudest minds.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Nada puede traerte la paz sino tú mismo.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'Nothing can bring you peace but yourself.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Algunos sienten la lluvia. Otros sólo se mojan.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'Some people feel the rain. Others just get wet.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'El pesimista ve dificultades en cada oportunidad. El optimista ve una oportunidad en cada dificultad.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'The pessimist sees difficulty in every opportunity. The optimist sees opportunity in every difficulty.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'No harás cosas increíbles sin un sueño increíble.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'You will not do incredible things without an incredible dream.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Nunca te arrepientas de tu pasado. Más bien, acéptalo como el maestro que es.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'Never regret your past. Rather, embrace it as the teacher that it is.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Reconocer lo bueno que ya tienes en tu vida es la base de toda abundancia.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'Acknowledging the good that you already have in your life is the foundation for all abundance.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Creo que la mejor manera de amar a alguien no es cambiarlo, sino ayudarlo a revelar la mejor versión de sí mismo.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'I find the best way to love someone is not to change them, but instead, help them reveal the greatest version of themselves.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Ten un corazón que nunca se endurezca, y un temperamento que nunca se canse, y un toque que nunca duela.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'Have a heart that never hardens, and a temper that never tires, and a touch that never hurts.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Si fijas tus objetivos ridículamente altos y es un fracaso, fracasarás por encima del éxito de los demás.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'If you set your goals ridiculously high and its a failure, you will fail above everyone elses success.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Lo que hay detrás de nosotros y lo que hay delante de nosotros son cosas minúsculas comparadas con lo que hay dentro de nosotros.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'What lies behind us and what lies before us are tiny matters compared to what lies within us.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Podemos añadir a nuestro carácter cada día, o podemos desperdiciar nuestras energías en distracciones.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'We can either add to our character each day, or we can fritter away our energies in distractions.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Una persona sin sentido del humor es como un carro sin resortes, sacudido por cada piedra en el camino.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'A person without a sense of humor is like a wagon without springs, jolted by every pebble in the road.')->value('id'),
            ],
            [
                'id' => Str::uuid(),
                'text' => 'Despierta. Presta atención, no desperdicies tu vida.',
                'text_id' => DB::table('texts')
                ->where('text', '=', 'Awaken. Take heed, do not squander your life.')->value('id'),
            ],
        ];

        DB::table('estexts')->insert($estexts);
    }
}
