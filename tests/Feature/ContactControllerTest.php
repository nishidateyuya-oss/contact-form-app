<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_お問い合わせ入力ページ表示(): void
    {
        Category::factory()->count(5)->create();
        Tag::factory()->count(5)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHasAll(['categories', 'tags']);
    }

    public function test_お問い合わせ保存(): void
    {
        $category = Category::factory()->create(['content' => '返品']);
        $tags = Tag::factory()->count(3)->create();
        $testContact = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => '1',
            'email' => 'tarou@example.com',
            'tel' => '08011112222',
            'address' => '茨城県 水戸市',
            'building' => '納豆ビル',
            'category_id' => $category->id,
            'detail' => '納豆の返品',
            'tag_ids' => $tags->pluck('id')->toArray(),
        ];

        $response = $this->post('/contacts/confirm', $testContact);

        $response->assertOk();
    }
}
