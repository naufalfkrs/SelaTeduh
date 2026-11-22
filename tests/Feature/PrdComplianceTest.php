<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureGuestToken;
use App\Models\BreathingLog;
use App\Models\JournalEntry;
use App\Models\MoodLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Memastikan perilaku aplikasi sesuai PRD SelaTeduh.
 */
class PrdComplianceTest extends TestCase
{
    use RefreshDatabase;

    private const GUEST_A = 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
    private const GUEST_B = 'BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBB';

    private function asGuest(string $token): static
    {
        // withCredentials: agar cookie juga ikut terkirim pada request JSON (seperti fetch di peramban)
        return $this->withCredentials()->withCookie(EnsureGuestToken::COOKIE, $token);
    }

    public function test_first_visit_gets_anonymous_guest_cookie(): void
    {
        $this->get('/')->assertOk()->assertCookie(EnsureGuestToken::COOKIE);
    }

    public function test_guest_can_record_mood_and_journal_without_account(): void
    {
        $this->asGuest(self::GUEST_A)
            ->post('/mood', ['mood' => 'Tenang', 'triggers' => ['Waktu Sendiri'], 'note' => 'Sore yang pelan.'])
            ->assertRedirect();

        $this->asGuest(self::GUEST_A)
            ->post('/jurnal', ['title' => 'Catatan tamu', 'content' => 'Menulis tanpa akun.'])
            ->assertRedirect('/jurnal');

        $mood = MoodLog::first();
        $this->assertNull($mood->user_id);
        $this->assertSame(self::GUEST_A, $mood->guest_token);
        $this->assertNull(JournalEntry::first()->user_id);

        $this->asGuest(self::GUEST_A)->get('/mood')->assertOk()->assertSee('Sore yang pelan.');
        $this->asGuest(self::GUEST_A)->get('/jurnal')->assertOk()->assertSee('Catatan tamu');
        $this->asGuest(self::GUEST_A)->get('/jurnal/export')->assertOk()->assertDownload();
    }

    public function test_guest_cannot_see_or_change_another_guests_notes(): void
    {
        $this->asGuest(self::GUEST_A)->post('/mood', ['mood' => 'Resah', 'note' => 'Rahasia tamu A']);
        $this->asGuest(self::GUEST_A)->post('/jurnal', ['content' => 'Jurnal rahasia tamu A']);
        $mood = MoodLog::first();
        $entry = JournalEntry::first();

        $this->asGuest(self::GUEST_B)->get('/mood')->assertOk()->assertDontSee('Rahasia tamu A');
        $this->asGuest(self::GUEST_B)->get('/jurnal')->assertOk()->assertDontSee('Jurnal rahasia tamu A');
        $this->asGuest(self::GUEST_B)->delete("/mood/{$mood->id}")->assertForbidden();
        $this->asGuest(self::GUEST_B)->put("/mood/{$mood->id}", ['mood' => 'Tenang'])->assertForbidden();
        $this->asGuest(self::GUEST_B)->delete("/jurnal/{$entry->id}")->assertForbidden();

        $this->asGuest(self::GUEST_A)->delete("/mood/{$mood->id}")->assertRedirect();
        $this->assertModelMissing($mood);
    }

    public function test_user_cannot_change_another_users_notes_and_guest_cannot_touch_user_notes(): void
    {
        [$owner, $other] = User::factory()->count(2)->create();
        $mood = MoodLog::create(['user_id' => $owner->id, 'mood' => 'Lelah', 'logged_date' => today()]);
        $entry = JournalEntry::create(['user_id' => $owner->id, 'content' => 'Punya pemilik']);

        $this->actingAs($other)->delete("/mood/{$mood->id}")->assertForbidden();
        $this->actingAs($other)->delete("/jurnal/{$entry->id}")->assertForbidden();

        $this->actingAs($owner)->put("/mood/{$mood->id}", ['mood' => 'Tenang', 'note' => 'Sudah lebih baik'])->assertRedirect();
        $this->assertSame('Tenang', $mood->fresh()->mood);
        $this->actingAs($owner)->delete("/jurnal/{$entry->id}")->assertRedirect('/jurnal');
        $this->assertModelMissing($entry);
    }

    public function test_guest_cannot_touch_registered_users_notes(): void
    {
        $owner = User::factory()->create();
        $mood = MoodLog::create(['user_id' => $owner->id, 'mood' => 'Lelah', 'logged_date' => today()]);

        $this->asGuest(self::GUEST_A)->delete("/mood/{$mood->id}")->assertForbidden();
        $this->assertModelExists($mood);
    }

    public function test_guest_notes_move_to_account_after_register(): void
    {
        $this->asGuest(self::GUEST_A)->post('/mood', ['mood' => 'Biasa Saja']);
        $this->asGuest(self::GUEST_A)->post('/jurnal', ['content' => 'Sebelum punya akun']);
        $this->asGuest(self::GUEST_A)->postJson('/latihan/selesai', ['rhythm_name' => 'Rileks (4-7-8)', 'cycles' => 4, 'duration' => '1 menit 16 detik']);

        $this->asGuest(self::GUEST_A)->post('/register', [
            'name' => 'Sela',
            'email' => 'sela@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ])->assertRedirect('/');

        $user = User::where('email', 'sela@example.com')->first();
        $this->assertSame(1, $user->moodLogs()->count());
        $this->assertSame(1, $user->journalEntries()->count());
        $this->assertSame(1, $user->breathingLogs()->count());
        $this->assertSame(0, MoodLog::whereNotNull('guest_token')->count());
    }

    public function test_login_and_logout_flow(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/login', ['email' => $user->email, 'password' => 'rahasia123'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_quick_check_in_saves_without_reload_via_json(): void
    {
        $this->asGuest(self::GUEST_A)
            ->postJson('/mood', ['mood' => 'Lelah', 'note' => 'Dari beranda'])
            ->assertCreated()
            ->assertJson(['status' => 'ok', 'mood' => 'Lelah']);

        $this->assertDatabaseHas('mood_logs', ['mood' => 'Lelah', 'note' => 'Dari beranda']);
    }

    public function test_mood_validation_follows_prd(): void
    {
        $this->asGuest(self::GUEST_A)->postJson('/mood', ['mood' => 'Senang'])->assertUnprocessable()->assertJsonValidationErrors('mood');
        $this->asGuest(self::GUEST_A)->postJson('/mood', ['mood' => 'Tenang', 'note' => str_repeat('a', 501)])->assertJsonValidationErrors('note');
        $this->asGuest(self::GUEST_A)->postJson('/mood', ['mood' => 'Tenang', 'logged_at' => now()->addDay()->toDateTimeString()])->assertJsonValidationErrors('logged_at');
    }

    public function test_mood_logging_time_can_be_adjusted_manually_within_today(): void
    {
        Carbon::setTestNow(Carbon::today()->setTime(22, 0));
        $when = Carbon::today()->setTime(21, 30);

        // Tidak bisa dimundurkan ke hari kemarin
        $this->asGuest(self::GUEST_A)->postJson('/mood', ['mood' => 'Resah', 'logged_at' => $when->copy()->subDay()->format('Y-m-d\TH:i')])
            ->assertJsonValidationErrors('logged_at');

        $this->asGuest(self::GUEST_A)->post('/mood', ['mood' => 'Resah', 'logged_at' => $when->format('Y-m-d\TH:i')]);

        $log = MoodLog::first();
        $this->assertSame($when->toDateString(), $log->logged_date->toDateString());
        $this->assertSame('21:30', $log->logged_at->format('H:i'));
        Carbon::setTestNow();
    }

    public function test_mood_can_only_be_recorded_once_per_day(): void
    {
        $this->asGuest(self::GUEST_A)->postJson('/mood', ['mood' => 'Lelah'])->assertCreated();

        // Klik ulang dari Beranda: tidak membuat catatan baru, mengembalikan catatan yang ada
        $this->asGuest(self::GUEST_A)->postJson('/mood', ['mood' => 'Tenang'])
            ->assertStatus(409)
            ->assertJson(['status' => 'exists', 'mood' => 'Lelah']);

        // Kirim dari form halaman Mood: juga tidak menambah catatan
        $this->asGuest(self::GUEST_A)->post('/mood', ['mood' => 'Resah'])->assertRedirect('/mood');
        $this->assertSame(1, MoodLog::count());

        // Halaman Mood menampilkan form "ubah catatan hari ini"
        $this->asGuest(self::GUEST_A)->get('/mood')->assertOk()
            ->assertSee('Rasamu hari ini sudah tercatat.')
            ->assertSee('Simpan Perubahan');

        // Tamu lain tetap bisa mencatat
        $this->asGuest(self::GUEST_B)->postJson('/mood', ['mood' => 'Tenang'])->assertCreated();
    }

    public function test_only_todays_mood_can_be_edited_past_ones_can_only_be_deleted(): void
    {
        $today = MoodLog::create(['guest_token' => self::GUEST_A, 'mood' => 'Lelah', 'logged_date' => today(), 'logged_at' => now()]);
        $past = MoodLog::create(['guest_token' => self::GUEST_A, 'mood' => 'Resah', 'logged_date' => today()->subDays(2), 'logged_at' => now()->subDays(2)]);

        $this->asGuest(self::GUEST_A)->put("/mood/{$today->id}", ['mood' => 'Tenang', 'triggers' => ['Waktu Sendiri']])->assertRedirect();
        $this->assertSame('Tenang', $today->fresh()->mood);
        $this->assertSame(['Waktu Sendiri'], $today->fresh()->triggers);

        $this->asGuest(self::GUEST_A)->put("/mood/{$past->id}", ['mood' => 'Tenang'])->assertSessionHasErrors('mood');
        $this->assertSame('Resah', $past->fresh()->mood);

        $this->asGuest(self::GUEST_A)->delete("/mood/{$past->id}")->assertRedirect();
        $this->assertModelMissing($past);
    }

    public function test_weekly_narrative_uses_prd_rule_for_school_and_sleep_fatigue(): void
    {
        MoodLog::create(['guest_token' => self::GUEST_A, 'mood' => 'Tenang', 'triggers' => ['Tugas/Sekolah'], 'logged_date' => today()->subDay(), 'logged_at' => now()->subDay()]);
        $this->asGuest(self::GUEST_A)->post('/mood', ['mood' => 'Lelah', 'triggers' => ['Tugas/Sekolah', 'Tidur Kurang']]);

        $this->asGuest(self::GUEST_A)->get('/mood')
            ->assertOk()
            ->assertSee('Di awal minggu kamu sempat merasa lelah dan resah karena tugas.');
    }

    public function test_breathing_session_is_stored_as_breathing_log(): void
    {
        $this->asGuest(self::GUEST_A)
            ->postJson('/latihan/selesai', ['rhythm_name' => 'Sederhana (4-4)', 'cycles' => 6, 'duration' => '0 menit 48 detik'])
            ->assertOk();

        $this->assertSame(1, BreathingLog::count());
        $this->asGuest(self::GUEST_A)->get('/mood')->assertSee('Sederhana (4-4)');
    }

    public function test_breathing_rhythms_match_prd_table(): void
    {
        $rhythms = collect($this->get('/latihan')->viewData('rhythms'))->keyBy('id');

        $pick = fn ($r) => [$r['inhale'], $r['hold'], $r['exhale'], $r['cycles']];

        $this->assertSame([4, 7, 8, 4], $pick($rhythms['rileks']));
        $this->assertSame([4, 4, 4, 5], $pick($rhythms['fokus']));
        $this->assertSame([4, 0, 4, 6], $pick($rhythms['sederhana']));
    }

    public function test_journal_input_is_escaped_against_xss(): void
    {
        $this->asGuest(self::GUEST_A)->post('/jurnal', ['title' => '<script>alert(1)</script>', 'content' => '<img src=x onerror=alert(1)>']);

        $this->asGuest(self::GUEST_A)->get('/jurnal')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    }

    public function test_home_shows_hero_actions_and_emergency_directory(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Mulai dari sini')
            ->assertSee('Jelajahi sumber bantuan')
            ->assertSee('SEJIWA')
            ->assertSee('Yayasan Pulih');
    }
}
