<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Keyword;
use App\Models\News;
use Illuminate\Support\Facades\Http;

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

            $searchTerm = urlencode($keyword->term);
            $rssUrl = "https://news.google.com/rss/search?q={$searchTerm}&hl=id&gl=ID&ceid=ID:id";

            $response = Http::timeout(15)->get($rssUrl);

            if ($response->failed()) {
                $this->error("Gagal fetch RSS untuk {$keyword->term}");
                continue;
            }

            $xml = simplexml_load_string($response->body());
            if (!$xml) continue;

            $newCount = 0;

            foreach ($xml->channel->item as $item) {
                $url = (string) $item->link;

                // Skip kalau sudah ada
                if (News::where('url', $url)->exists()) {
                    continue;
                }

                News::create([
                    'keyword_id'   => $keyword->id,
                    'title'        => (string) $item->title,
                    'description'  => strip_tags((string) $item->description),
                    'url'          => $url,
                    'source'       => $this->extractSource((string) $item->description),
                    'published_at' => \Carbon\Carbon::parse((string) $item->pubDate),
                ]);

                $newCount++;
            }

            $this->info("✅ Berhasil tambah {$newCount} berita baru untuk '{$keyword->term}'");
        }

        $this->info('🎉 Semua keyword selesai di-update!');
    }
    private function extractSource(string $description): ?string
    {
        // Google News biasanya menyertakan sumber di akhir description
        if (preg_match('/-\s*(.+?)\s*$/u', $description, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }
}
