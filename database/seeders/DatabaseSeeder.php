<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Specialization;
use App\Models\Question;
use App\Models\Choice;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Languages
        $en = Language::firstOrCreate(['code' => 'en'], ['name' => 'English']);
        $ar = Language::firstOrCreate(['code' => 'ar'], ['name' => 'Arabic']);

        // Specializations
        $specs = [
            ['slug' => 'solutions-architect', 'name' => '🏗️ | Solutions Architect'],
            ['slug' => 'system-architect', 'name' => '🖥️ | System Architect'],
            ['slug' => 'security-engineer', 'name' => '🛡️ | Security Engineer'],
            ['slug' => 'software-engineer', 'name' => '💻 | Software Engineer'],
            ['slug' => 'full-stack', 'name' => '⚙️ | Full-Stack Developer'],
            ['slug' => 'backend', 'name' => '🔧 | Backend Developer'],
            ['slug' => 'frontend', 'name' => '🎨 | Frontend Developer'],
            ['slug' => 'mobile', 'name' => '📱 | Mobile Developer'],
            ['slug' => 'backend', 'name' => '🔧 | Backend Developer'],
           

        ];

        foreach ($specs as $specData) {
            $spec = Specialization::firstOrCreate(
                ['slug' => $specData['slug']],
                ['name' => $specData['name']]
            );
            
            // Seed Dummy Questions for English
            if (Question::where('specialization_id', $spec->id)->count() < 3) {
                for ($i = 1; $i <= 5; $i++) {
                    $q = Question::create([
                        'specialization_id' => $spec->id,
                        'language_id' => $en->id,
                        'question_text' => "Sample Question {$i} for {$spec->name}?",
                    ]);
                    
                    Choice::create(['question_id' => $q->id, 'choice_text' => 'Correct Answer', 'is_correct' => true]);
                    Choice::create(['question_id' => $q->id, 'choice_text' => 'Wrong Answer 1', 'is_correct' => false]);
                    Choice::create(['question_id' => $q->id, 'choice_text' => 'Wrong Answer 2', 'is_correct' => false]);
                    Choice::create(['question_id' => $q->id, 'choice_text' => 'Wrong Answer 3', 'is_correct' => false]);
                }
            }
        }
    }
}
