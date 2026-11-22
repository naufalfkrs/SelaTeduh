<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Database\Seeders\ArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render(): void
    {
        $this->seed(ArticleSeeder::class);
        $article = Article::first();

        $this->get('/')->assertOk()->assertSee('Kamu nggak harus baik-baik saja setiap saat.');
        $this->get('/jelajahi')->assertOk()->assertSee('Memahami diri, satu langkah demi satu.');
        $this->get('/jelajahi?kategori=Kecemasan&q=cemas')->assertOk();
        $this->get('/jelajahi/'.$article->slug)->assertOk()->assertSee($article->title);
        $this->get('/latihan')->assertOk()->assertSee('Ambil jeda sejenak.');
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_private_pages_render_for_logged_in_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/mood', ['mood' => 'Lelah', 'triggers' => ['Tugas/Sekolah']])->assertRedirect();
        $this->actingAs($user)->post('/jurnal', ['content' => 'Hari ini cukup panjang.'])->assertRedirect('/jurnal');

        $this->actingAs($user)->get('/mood')->assertOk()->assertSee('Kenali pola perasaanmu.')->assertSee('Lelah');
        $this->actingAs($user)->get('/jurnal')->assertOk()->assertSee('Ruang ceritamu.')->assertSee('Hari ini cukup panjang.');
    }

    public function test_guests_can_open_mood_and_journal_without_login(): void
    {
        $this->get('/mood')->assertOk()->assertSee('Mode Tamu aktif.');
        $this->get('/jurnal')->assertOk()->assertSee('Mode Tamu aktif.');
    }
}
