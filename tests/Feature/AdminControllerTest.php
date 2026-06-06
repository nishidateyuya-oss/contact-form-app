<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Carbon\carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_お問い合わせ一覧検索(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['content' => 'test_content']);
        Contact::factory()->create([
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'yamada@.com',
            'category_id' => $category->id,
            'created_at' => carbon::parse('2026-06-02 09:00:00'),
        ]);
        Contact::factory()->create([
            'first_name' => '田中',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'tanaka@.com',
            'created_at' => carbon::parse('2026-06-01 09:00:00'),
        ]);

        $response = $this->actingAs($user)->get("/admin?keword=山田&gender=1&category_id={$category->id}&date=2026-06-02");

        $response->assertOk();
        $response->assertSee('山田');
        $response->assertDontSee('田中');
    }

    public function test_管理者の画面アクセス(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_未認証ユーザーはリダイレクト(): void
    {

        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }
}
