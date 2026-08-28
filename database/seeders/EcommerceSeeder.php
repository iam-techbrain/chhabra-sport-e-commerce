<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Tag;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\SizeGuide;
use App\Models\Review;
use App\Models\Product;

class EcommerceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Brands
        $brands = [
            ['name' => 'Yonex', 'slug' => 'yonex', 'description' => 'World Leader in Badminton & Tennis Gear'],
            ['name' => 'Head', 'slug' => 'head', 'description' => 'Premium Tennis & Squash Equipment'],
            ['name' => 'Babolat', 'slug' => 'babolat', 'description' => 'Explosive Power Tennis Racquets & Accessories'],
            ['name' => 'SS', 'slug' => 'ss', 'description' => 'Master English Willow Bats & Protective Gear'],
            ['name' => 'Li-Ning', 'slug' => 'li-ning', 'description' => 'High Elasticity Pro Badminton Equipment'],
            ['name' => 'Asics', 'slug' => 'asics', 'description' => 'Non-Marking Indoor & Court Shoes'],
            ['name' => 'Adidas', 'slug' => 'adidas', 'description' => 'Football Boots & Performance Sportswear'],
            ['name' => 'Cosco', 'slug' => 'cosco', 'description' => 'Fitness Kits & Training Equipment'],
        ];

        foreach ($brands as $b) {
            Brand::firstOrCreate(['slug' => $b['slug']], $b);
        }

        // 2. Seed Tags
        $tags = [
            ['name' => 'BESTSELLER', 'slug' => 'bestseller'],
            ['name' => 'PRO CHOICE', 'slug' => 'pro-choice'],
            ['name' => 'NEW', 'slug' => 'new'],
            ['name' => 'POPULAR', 'slug' => 'popular'],
            ['name' => 'GYM ESSENTIAL', 'slug' => 'gym-essential'],
            ['name' => 'TOURNAMENT CLASS', 'slug' => 'tournament-class'],
        ];

        foreach ($tags as $t) {
            Tag::firstOrCreate(['slug' => $t['slug']], $t);
        }

        // 3. Seed Attributes & Values
        $weightAttr = Attribute::firstOrCreate(['code' => 'weight'], ['name' => 'Weight Category']);
        AttributeValue::firstOrCreate(['attribute_id' => $weightAttr->id, 'value' => '4U (80-84g)']);
        AttributeValue::firstOrCreate(['attribute_id' => $weightAttr->id, 'value' => '3U (85-89g)']);
        AttributeValue::firstOrCreate(['attribute_id' => $weightAttr->id, 'value' => '300g Unstrung']);

        $gripAttr = Attribute::firstOrCreate(['code' => 'grip_size'], ['name' => 'Grip Size']);
        AttributeValue::firstOrCreate(['attribute_id' => $gripAttr->id, 'value' => 'G4']);
        AttributeValue::firstOrCreate(['attribute_id' => $gripAttr->id, 'value' => 'G5']);

        // 4. Seed Size Guides
        SizeGuide::firstOrCreate(['title' => 'Badminton Racquet Weight & Grip Size Guide'], [
            'category_name' => 'badminton',
            'chart_json' => json_encode([
                'headers' => ['Weight Symbol', 'Gram Range', 'Player Recommendation'],
                'rows' => [
                    ['4U', '80g - 84g', 'Speed & Fast Reaction (Doubles/Singles)'],
                    ['3U', '85g - 89g', 'Heavy Power Smashers'],
                    ['2U', '90g - 94g', 'Training / Heavy Power']
                ]
            ])
        ]);

        SizeGuide::firstOrCreate(['title' => 'Cricket Bat Willow & Weight Chart'], [
            'category_name' => 'cricket',
            'chart_json' => json_encode([
                'headers' => ['Willow Grade', 'Approx Weight', 'Bat Grain Profile'],
                'rows' => [
                    ['Grade 1 English Willow', '1160g - 1200g', '7 - 11 Straight Grains'],
                    ['Grade 2 English Willow', '1180g - 1220g', '5 - 8 Grains'],
                    ['Kashmir Willow', '1200g - 1250g', 'Standard Durable Practice']
                ]
            ])
        ]);

        // 5. Seed Reviews for existing products
        $p1 = Product::where('code_id', 'p1')->first();
        if ($p1) {
            Review::firstOrCreate(['product_id' => $p1->id, 'comment' => 'Insane power for smash shots! Stringing done by Chhabra Sports was spot on.'], [
                'user_name' => 'Vikram Sharma',
                'rating' => 5,
                'is_verified' => true,
                'is_approved' => true
            ]);
            Review::firstOrCreate(['product_id' => $p1->id, 'comment' => 'Original Yonex racquet with genuine hologram. Fast delivery to Patna.'], [
                'user_name' => 'Amit Raj',
                'rating' => 5,
                'is_verified' => true,
                'is_approved' => true
            ]);
        }
    }
}
