<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    
    public function run(): void
    {
        // 既存データの存在チェック
        if (Category::count() === 0) {
            $this->command->error('先にCategoriesテーブルにデータを投入してください。');

            return;
        }

        $tags = Tag::all();

        // 【条件1, 4】 factory() と count(20) を使用
        Contact::factory()
            ->count(20)
            ->create()
            ->each(function ($contact) use ($tags) {
                // 【条件4】 作成された各Contactに対して、既存のタグから1〜3件をランダムに選んで attach()
                if ($tags->isNotEmpty()) {
                    $randomTags = $tags->random(rand(1, 3));
                    $contact->tags()->attach($randomTags);
                }
            });
    }
}
