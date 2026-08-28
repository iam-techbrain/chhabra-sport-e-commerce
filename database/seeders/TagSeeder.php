<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = ['BESTSELLER', 'PRO CHOICE', 'NEW ARRIVAL', 'HOT DEAL', 'LIMITED EDITION', 'SALE', 'GYM ESSENTIAL'];

        foreach ($tags as $t) {
            Tag::updateOrCreate(['slug' => Str::slug($t)], ['name' => $t]);
        }
    }
}
