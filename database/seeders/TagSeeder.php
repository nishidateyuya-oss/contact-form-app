<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tag::factory()->create([
            'name' => '質問',
        ]);

        Tag::factory()->create([
            'name' => '要望',
        ]);

        Tag::factory()->create([
            'name' => '不具合報告',
        ]);

        Tag::factory()->create([
            'name' => 'ご意見',
        ]);

        Tag::factory()->create([
            'name' => 'その他',
        ]);
    }
}
