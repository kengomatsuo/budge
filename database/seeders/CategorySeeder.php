<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $defaultCategories = [
            ['name' => 'Food', 'icon' => '🍔', 'color' => '#FF6B6B', 'is_default' => true],
            ['name' => 'Transportation', 'icon' => '🚗', 'color' => '#4ECDC4', 'is_default' => true],
            ['name' => 'Shopping', 'icon' => '🛍️', 'color' => '#45B7D1', 'is_default' => true],
            ['name' => 'Entertainment', 'icon' => '🎬', 'color' => '#FFA07A', 'is_default' => true],
            ['name' => 'Healthcare', 'icon' => '🏥', 'color' => '#98D8C8', 'is_default' => true],
            ['name' => 'Bills & Utilities', 'icon' => '💡', 'color' => '#F7DC6F', 'is_default' => true],
            ['name' => 'Education', 'icon' => '📚', 'color' => '#BB8FCE', 'is_default' => true],
            ['name' => 'Housing', 'icon' => '🏠', 'color' => '#85C1E2', 'is_default' => true],
            ['name' => 'Personal Care', 'icon' => '💅', 'color' => '#F8B4D9', 'is_default' => true],
            ['name' => 'Other', 'icon' => '📝', 'color' => '#95A5A6', 'is_default' => true],
        ];

        foreach ($users as $user) {
            foreach ($defaultCategories as $category) {
                Category::create([
                    'user_id' => $user->id,
                    'name' => $category['name'],
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'is_default' => $category['is_default'],
                ]);
            }
        }
    }
}
