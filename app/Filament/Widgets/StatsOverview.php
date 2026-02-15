<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Support\Colors\Color;


class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {

        $result = Result::query()->whereIn('status', ['validated', 'unvalidated'])->get();
        return [
            Stat::make('Total URL', $result->count())
                ->description('Jumlah url terdaftar')
                ->color(Color::hex('#ffffff'))  // atau Color::rgb(52, 168, 83)
                ->extraAttributes([
                    'style' => '
            background-color:rgba(66, 133, 244);
            box-shadow: 0 3px 8px rgba(176,196,222);
            border-radius: 12px;
        ',  // background sudah di-handle ->color(), jadi hapus background-color di sini
                ]),
            Stat::make('Valid', $result->where('status', 'validated')->count())
                ->description('Jumlah berita hoaxs terverifikasi')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            background-color:rgba(52, 168, 83);
            box-shadow: 0 3px 8px rgba(176,196,222);
            border-radius: 12px;
        ',  // background sudah di-handle ->color(), jadi hapus background-color di sini
                ]),
            Stat::make('Tidak Valid', $result->where('status', 'unvalidated')->count())
                ->description('Jumlah tidak valid ')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            background-color:rgba(234, 67, 53);
            box-shadow: 0 3px 8px rgba(176,196,222);
            border-radius: 12px;
        ',  // background sudah di-handle ->color(), jadi hapus background-color di sini
                ]),
        ];
    }
}
