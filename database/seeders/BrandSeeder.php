<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Yonex', 'slug' => 'yonex', 'description' => 'World No. 1 Badminton & Tennis Equipment'],
            ['name' => 'Head', 'slug' => 'head', 'description' => 'Pro Tennis Racquets & Strings'],
            ['name' => 'Babolat', 'slug' => 'babolat', 'description' => 'High-Power Tennis Racquets'],
            ['name' => 'SS', 'slug' => 'ss', 'description' => 'Ton Reserve Edition Cricket Bats'],
            ['name' => 'SG', 'slug' => 'sg', 'description' => 'Test Players Wicket Keeping & Gloves'],
            ['name' => 'MRF', 'slug' => 'mrf', 'description' => 'Genius Grand Edition English Willow Bats'],
            ['name' => 'Li-Ning', 'slug' => 'li-ning', 'description' => 'High Elasticity Carbon Racquets'],
            ['name' => 'Asics', 'slug' => 'asics', 'description' => 'Non-Marking Indoor Gum Sole Shoes'],
            ['name' => 'Adidas', 'slug' => 'adidas', 'description' => 'Predator Elite FG Football Boots'],
            ['name' => 'Cosco', 'slug' => 'cosco', 'description' => 'Adjustable Rubber Dumbbells & Fitness']
        ];

        foreach ($brands as $b) {
            Brand::updateOrCreate(['slug' => $b['slug']], $b);
        }
    }
}
