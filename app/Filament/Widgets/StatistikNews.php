<?php

namespace App\Filament\Resources\NewsResource\Widgets;

use App\Models\News;
use App\Models\Keyword;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatistikNews extends BaseWidget
{
    protected function getStats(): array
    {
        $totalNews = News::count();
        $totalNewsWithTitle = News::whereNotNull('title')->count();
        $totalKeyword = Keyword::where('is_active', 1)->count();
        return [
            Stat::make('Total Berita', $totalNews)
                ->icon('heroicon-o-newspaper')
                ->description('Total berita yang tersimpan')
                ->extraAttributes([
                    'style' => '
                        box-shadow: 0 -4px 6px -2px rgba(59, 130, 246, 0.5);
                        border-radius:12px;',
                ]),

            Stat::make('Total Judul Berita', $totalNewsWithTitle)
                ->icon('heroicon-o-document-text')
                ->description('Berita dengan judul yang tersimpan')
                ->extraAttributes([
                    'style' => '
                        box-shadow: 0 -4px 6px -2px rgba(16, 185, 129, 0.4);
                        border-radius:12px;',
                ])
                // ->url($latestNews?->url)
                //->openUrlInNewTab()
                ->extraAttributes([
                    'style' => '
                        box-shadow: 0 -4px 6px -2px rgba(16, 185, 129, 0.4);
                        border-radius:12px;',
                ]),

            // Stat::make('Total Keyword', $totalKeyword?->name ?? '-')
            Stat::make('Total Keyword', $totalKeyword ?? 0)
                ->description('Total keyword yang terdaftar')
                ->icon('heroicon-o-tag')
                ->extraAttributes([
                    'style' => '
                        box-shadow: 0 -4px 6px -2px rgba(245, 158, 11, 0.4);
                        border-radius:12px;',
                ]),
        ];
    }
}
