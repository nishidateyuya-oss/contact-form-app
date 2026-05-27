<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

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
