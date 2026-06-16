<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_タグ新規登録(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/tags', ['name' => '新しいタグ']);

        $response->assertRedirect('/admin');
    }

    public function test_名前が同じタグは登録できない(): void
    {

        Tag::factory()->create(['name' => '新しいタグ']);

        $response = $this->post('/admin/tags', ['name' => '新しいタグ']);

        $response->assertSessionHasErrors('name');
    }

    public function test_文字数制限を超えたタグは登録できない(): void
    {
        $response = $this->post('/admin/tags', ['name' => str_repeat('あ', 51)]);

        $response->assertSessionHasErrors('name');
    }

    public function test_タグ詳細表示(): void
    {
        $tag = Tag::factory()->create(['name' => 'タグ']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/admin/tags/{$tag->id}/edit");

        $response->assertOk();
        $response->assertViewHas('tag');
    }

    public function test_タグ変更(): void
    {
        $tag = Tag::factory()->create(['name' => '変更前タグ']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", ['name' => '変更後タグ']);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '変更後タグ',
        ]);
    }

    public function test_タグ変更の名前維持は可能(): void
    {
        $tag = Tag::factory()->create(['name' => '変更前タグ']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", ['name' => '変更前タグ']);

        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => '変更前タグ',
        ]);
    }

    public function test_既に使われてるタグ名は変更拒否(): void
    {
        $tag = Tag::factory()->create(['name' => '変更前タグ']);
        $otherTag = Tag::factory()->create(['name' => '違うタグ']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", ['name' => '違うタグ']);

        $response->assertSessionHasErrors('name');
    }

    public function test_タグ削除(): void
    {
        $tag = Tag::factory()->create(['name' => '新しいタグ']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
            'name' => $tag->name,
        ]);
    }

    public function test_未認証ユーザーのタグ登録拒否(): void
    {
        $response = $this->post('/admin/tags', ['name' => '新しいタグ']);

        $response->assertRedirect('/login');
    }

    public function test_未認証ユーザーのタグ更新拒否(): void
    {
        $tag = Tag::factory()->create(['name' => '変更前タグ']);

        $response = $this->put("/admin/tags/{$tag->id}", ['name' => '変更後タグ']);

        $response->assertRedirect('/login');
    }

    public function test_未認証ユーザーのタグ削除拒否(): void
    {
        $tag = Tag::factory()->create(['name' => '新しいタグ']);

        $response = $this->delete("/admin/tags/{$tag->id}");

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => $tag->name,
        ]);
    }
}
