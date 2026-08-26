<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Badminton', 'slug' => 'badminton', 'icon' => '🏸', 'description' => 'Racquets, Shuttles & Grips'],
            ['name' => 'Tennis', 'slug' => 'tennis', 'icon' => '🎾', 'description' => 'Pro Racquets, Balls & Gut Strings'],
            ['name' => 'Cricket', 'slug' => 'cricket', 'icon' => '🏏', 'description' => 'English Willow Bats, Pads & Gloves'],
            ['name' => 'Court Shoes', 'slug' => 'shoes', 'icon' => '👟', 'description' => 'Non-Marking Gum Sole Shoes'],
            ['name' => 'Football', 'slug' => 'football', 'icon' => '⚽', 'description' => 'FG Studs, Balls & Shin Guards'],
            ['name' => 'Fitness & Gym', 'slug' => 'fitness', 'icon' => '🏋️', 'description' => 'Dumbbells, Resistance Bands & Accessories']
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
