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
    protected $description = 'Fetch berita otomatis dari Google News RSS setiap 3 jam';

    public function handle()
    {
        $this->info('🚀 Mulai fetch berita...');

        $keywords = Keyword::where('is_active', true)->get();

        foreach ($keywords as $keyword) {
            $this->info("🔍 Processing keyword: {$keyword->term}");

            $rssUrl = $this->buildRssUrl($keyword->term);

            $response = Http::timeout(15)->get($rssUrl);

            if ($response->failed()) {
                $this->error("❌ Gagal fetch RSS untuk {$keyword->term}");
                continue;
            }

            $xml = @simplexml_load_string($response->body());

            if (!$xml || !isset($xml->channel->item)) {
                $this->warn("⚠️ RSS tidak valid untuk {$keyword->term}");
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
                $source = $this->extractSource($descriptionHtml);

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

            $this->info("✅ {$newCount} berita baru untuk '{$keyword->term}'");
        }

        $this->info('🎉 Semua keyword selesai!');
    }

    private function buildRssUrl(string $term): string
    {
        $searchTerm = urlencode($term);
        return "https://news.google.com/rss/search?q={$searchTerm}&hl=id&gl=ID&ceid=ID:id";
    }

    /**
     * Bersihkan text dari HTML + entity
     */
    private function cleanText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        $text = trim($text);

        return $text;
    }

    /**
     * Extract source dari HTML description Google News
     */
    private function extractSource(string $html): ?string
    {
        // ambil isi <font> (biasanya sumber)
        if (preg_match('/<font.*?>(.*?)<\/font>/si', $html, $matches)) {
            $source = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $source = strip_tags($source);

            return Str::limit(trim($source), 255);
        }

        return null;
    }
}