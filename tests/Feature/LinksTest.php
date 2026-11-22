<?php

namespace Tests\Feature;

use Database\Seeders\ArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan semua tautan internal (navbar, footer, tombol bantuan) bisa dibuka,
 * bagian (#anchor) yang dituju benar-benar ada, dan footer tidak punya tautan ganda.
 */
class LinksTest extends TestCase
{
    use RefreshDatabase;

    private function internalLinks(string $html): array
    {
        preg_match_all('/href="([^"]+)"/', $html, $m);
        $base = rtrim(config('app.url'), '/');

        return collect($m[1])
            ->map(fn ($href) => html_entity_decode($href))
            ->filter(fn ($href) => str_starts_with($href, $base) || str_starts_with($href, '/'))
            ->map(fn ($href) => str_starts_with($href, $base) ? substr($href, strlen($base)) : $href)
            ->reject(fn ($href) => str_starts_with($href, '/build/'))
            ->map(fn ($href) => $href === '' || str_starts_with($href, '#') ? '/'.$href : $href)
            ->unique()
            ->values()
            ->all();
    }

    private function footerLinks(string $html): array
    {
        preg_match('/<footer.*?<\/footer>/s', $html, $m);

        return $this->internalLinks($m[0] ?? '');
    }

    public function test_every_internal_link_on_main_pages_works(): void
    {
        $this->seed(ArticleSeeder::class);

        $pages = ['/', '/jelajahi', '/latihan', '/jurnal', '/mood', '/tentang', '/privasi', '/bantuan'];
        $checked = [];

        foreach ($pages as $page) {
            foreach ($this->internalLinks($this->get($page)->getContent()) as $link) {
                if (isset($checked[$link])) {
                    continue;
                }
                $checked[$link] = true;

                [$path, $anchor] = array_pad(explode('#', $link, 2), 2, null);
                $response = $this->get($path === '' ? $page : $path);

                $this->assertContains($response->getStatusCode(), [200], "Tautan {$link} (dari {$page}) tidak bisa dibuka.");

                if ($anchor) {
                    $this->assertStringContainsString('id="'.$anchor.'"', $response->getContent(), "Bagian #{$anchor} tidak ada di {$path}.");
                }
            }
        }

        $this->assertNotEmpty($checked);
    }

    public function test_footer_links_are_unique(): void
    {
        preg_match('/<footer.*?<\/footer>/s', $this->get('/')->getContent(), $footer);
        preg_match_all('/<ul class="space-y-2.*?<\/ul>/s', $footer[0], $columns);
        preg_match_all('/href="([^"]+)"/', implode('', $columns[0]), $hrefs);

        $this->assertCount(13, $hrefs[1]);
        $this->assertSame(count($hrefs[1]), count(array_unique($hrefs[1])), 'Ada tautan ganda di kolom footer.');
        $this->assertNotEmpty($this->footerLinks($footer[0]));
    }

    public function test_info_pages_render(): void
    {
        $this->get('/tentang')->assertOk()->assertSee('id="filosofi"', false)->assertSee('id="nilai"', false);
        $this->get('/privasi')->assertOk()->assertSee('id="keamanan"', false)->assertSee('id="batasan"', false);
        $this->get('/bantuan')->assertOk()->assertSee('id="darurat"', false)->assertSee('id="sebaya"', false)->assertSee('119 ext. 8');
    }
}
