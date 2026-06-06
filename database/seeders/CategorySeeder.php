<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->create([
            'content' => '商品のお届けについて',
        ]);

        Category::factory()->create([
            'content' => '商品の交換について',
        ]);

        Category::factory()->create([
            'content' => '商品トラブル',
        ]);

        Category::factory()->create([
            'content' => 'ショップへのお問い合わせ',
        ]);

        Category::factory()->create([
            'content' => 'その他',
        ]);
    }
}
