<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Keyword;
use App\Models\News;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FetchNews extends Command
{
    protected $signature = 'app:fetch-news';
    protected $description = 'Fetch berita otomatis dari Google News, dan simpan URL TikTok, Instagram, Facebook setiap 3 jam';

    public function handle()
    {
        $this->info('🚀 Mulai fetch berita...');

        $keywords = Keyword::where('is_active', true)->get();

        foreach ($keywords as $keyword) {
            $this->info("🔍 Processing keyword: {$keyword->term}");

            $urls = $this->buildRssUrl($keyword->term);

            foreach ($urls as $platform => $rssUrl) {
                $this->info("🔍 Processing {$platform} untuk keyword: {$keyword->term}");

                // Hanya Google News & YouTube pakai Http::get()
                if (in_array($platform, ['google_news', 'youtube'])) {
                    $response = Http::timeout(15)->get($rssUrl);

                    if ($response->failed()) {
                        $this->error("❌ Gagal fetch {$platform} untuk {$keyword->term}");
                        continue;
                    }

                    $xml = @simplexml_load_string($response->body());

                    if (!$xml || !isset($xml->channel->item)) {
                        $this->warn("⚠️ RSS tidak valid untuk {$platform} dan keyword {$keyword->term}");
                        continue;
                    }

                    $newCount = 0;
                    foreach ($xml->channel->item as $item) {
                        $url = trim((string) $item->link);

                        if (!$url || News::where('url', $url)->exists()) {
                            continue;
                        }

                        $title = $this->cleanText((string) $item->title);
                        $descriptionHtml = (string) $item->description;
                        $description = $this->cleanText($descriptionHtml);
                        $source = $this->extractSource($descriptionHtml) ?? $platform;

                        News::create([
                            'keyword_id'   => $keyword->id,
                            'title'        => Str::limit($title, 255),
                            'description'  => Str::limit($description, 500),
                            'url'          => $url,
                            'source'       => $source,
                            'published_at' => Carbon::parse((string) $item->pubDate),
                        ]);

                        $newCount++;
                    }

                    $this->info("✅ {$newCount} berita baru untuk '{$keyword->term}' di {$platform}");
                } else {
                    // TikTok / Instagram / Facebook → simpan URL agar tampil di tabel
                    if (!News::where('url', $rssUrl)->exists()) {
                        News::create([
                            'keyword_id'   => $keyword->id,
                            'title'        => ucfirst($platform) . " posts untuk '{$keyword->term}'",
                            'description'  => "Link ke {$platform} untuk keyword '{$keyword->term}'",
                            'url'          => $rssUrl,
                            'source'       => $platform,
                            'published_at' => Carbon::now(),
                        ]);
                        $this->info("✅ URL {$platform} disimpan untuk '{$keyword->term}'");
                    } else {
                        $this->info("ℹ️ URL {$platform} sudah ada di database untuk '{$keyword->term}'");
                    }
                }
            }
        }

        $this->info('🎉 Semua keyword selesai!');
    }

    /**
     * Build RSS / URL per platform
     */
    private function buildRssUrl(string $term): array
    {
        $searchTerm = urlencode($term);

        return [
            'google_news' => "https://news.google.com/rss/search?q={$searchTerm}&hl=id&gl=ID&ceid=ID:id",
            'youtube'     => "https://www.youtube.com/feeds/videos.xml?search_query={$searchTerm}",
            'tiktok'      => "https://www.tiktok.com/tag/{$searchTerm}",
            'instagram'   => "https://www.instagram.com/explore/tags/{$searchTerm}/",
            'facebook'    => "https://www.facebook.com/search/posts/?q={$searchTerm}",
        ];
    }

    /**
     * Bersihkan text dari HTML + entity
     */
    private function cleanText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        return trim($text);
    }

    /**
     * Extract source dari HTML description Google News
     */
    private function extractSource(string $html): ?string
    {
        if (preg_match('/<font.*?>(.*?)<\/font>/si', $html, $matches)) {
            $source = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $source = strip_tags($source);
            return Str::limit(trim($source), 255);
        }

        return null;
    }
}
