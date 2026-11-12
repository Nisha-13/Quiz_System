<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

    /**
     * The default password used when none is provided.
     */
    public static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }
}
