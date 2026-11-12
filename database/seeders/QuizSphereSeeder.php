<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Mcq;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class QuizSphereSeeder extends Seeder
{
    /**
     * Seed the demo data used by the QuizSphere demo/portfolio build.
     *
     * Mirrors database/quiz_system.sql so the app can be installed with a
     * single `php artisan migrate:fresh --seed` instead of a phpMyAdmin import.
     */
    public function run(): void
    {
        $admin = Admin::updateOrCreate(
            ['name' => 'admin'],
            ['password' => Hash::make('admin123')]
        );

        User::updateOrCreate(
            ['email' => 'demoUser@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password123'),
                'active' => 2,
            ]
        );

        $categories = [
            'Web Development' => 'admin',
            'Programming' => 'admin',
            'General Knowledge' => 'admin',
            'Science' => 'admin',
        ];

        $categoryIds = [];
        foreach ($categories as $name => $creator) {
            $categoryIds[$name] = Category::updateOrCreate(
                ['name' => $name],
                ['creator' => $creator]
            )->id;
        }

        $quizzes = [
            'HTML-Basics' => 'Web Development',
            'CSS-Fundamentals' => 'Web Development',
            'PHP-Basics' => 'Programming',
            'World-Capitals' => 'General Knowledge',
            'Human-Body' => 'Science',
        ];

        $quizIds = [];
        foreach ($quizzes as $name => $category) {
            $quizIds[$name] = Quiz::updateOrCreate(
                ['name' => $name, 'category_id' => $categoryIds[$category]],
                []
            )->id;
        }

        $mcqs = [
            'HTML-Basics' => [
                ['What does HTML stand for?', 'Hyper Text Markup Language', 'Home Tool Markup Language', 'Hyperlinks and Text Markup Language', 'High Text Machine Language', 'a'],
                ['Which tag is used for the largest heading?', '<h6>', '<head>', '<h1>', '<heading>', 'c'],
                ['Which tag creates a hyperlink?', '<link>', '<a>', '<href>', '<url>', 'b'],
                ['Who maintains the HTML specification?', 'WHATWG / W3C', 'Microsoft', 'Google', 'IEEE', 'a'],
                ['Which attribute specifies images for screen readers?', 'title', 'alt', 'aria-img', 'desc', 'b'],
            ],
            'CSS-Fundamentals' => [
                ['What does CSS stand for?', 'Creative Style Sheets', 'Cascading Style Sheets', 'Computer Style Sheets', 'Colorful Style Sheets', 'b'],
                ['Which property changes text color?', 'font-color', 'text-color', 'color', 'font-style', 'c'],
                ['How do you include an external stylesheet?', '<style src="...">', '<script src="...">', '<stylesheet>...</stylesheet>', '<link rel="stylesheet" href="...">', 'd'],
                ['Which symbol starts an ID selector?', 'A dot ( . )', 'A hash ( # )', 'An ampersand ( & )', 'An asterisk ( * )', 'b'],
            ],
            'PHP-Basics' => [
                ['What does PHP stand for today?', 'Personal Home Page', 'PHP: Hypertext Preprocessor', 'Preprocessed Home Pages', 'Private Hosting Protocol', 'b'],
                ['Which symbol starts all PHP variables?', '$', '@', '#', '&', 'a'],
                ['Which function prints text in PHP?', 'write()', 'say()', 'echo', 'display()', 'c'],
                ['How does a PHP file end?', '.ph', '.php', '.html', '.php3', 'b'],
            ],
            'World-Capitals' => [
                ['What is the capital of France?', 'Berlin', 'Madrid', 'Paris', 'Rome', 'c'],
                ['What is the capital of Japan?', 'Kyoto', 'Tokyo', 'Osaka', 'Nagoya', 'b'],
                ['What is the capital of Australia?', 'Sydney', 'Melbourne', 'Canberra', 'Perth', 'c'],
                ['What is the capital of Canada?', 'Toronto', 'Ottawa', 'Vancouver', 'Montreal', 'b'],
            ],
            'Human-Body' => [
                ['Which organ pumps blood through the body?', 'Lungs', 'Heart', 'Liver', 'Kidneys', 'b'],
                ['How many bones does an adult human have?', '156', '206', '256', '306', 'b'],
                ['Which organ controls the nervous system?', 'Brain', 'Spine', 'Stomach', 'Eyes', 'a'],
            ],
        ];

        foreach ($mcqs as $quizName => $questions) {
            $quiz = Quiz::where('name', $quizName)->first();
            foreach ($questions as [$question, $a, $b, $c, $d, $correct]) {
                Mcq::updateOrCreate(
                    ['quiz_id' => $quiz->id, 'question' => $question],
                    [
                        'option_a' => $a,
                        'option_b' => $b,
                        'option_c' => $c,
                        'option_d' => $d,
                        'correct_ans' => $correct,
                        'admin_id' => $admin->id,
                        'category_id' => $quiz->category_id,
                    ]
                );
            }
        }
    }
}
