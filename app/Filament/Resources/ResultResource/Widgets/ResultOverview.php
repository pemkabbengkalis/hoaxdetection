<?php

namespace App\Filament\Resources\ResultResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Result;
use Filament\Support\Colors\Color;


class ResultOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Stat::make('Target Result', fn() => Result::count())
            //     ->icon('heroicon-o-globe-alt')
            //     ->description('Jumlah keseluruhan result yang')
            //     ->color('white')
            //     ->extraAttributes([
            //         'style' => '
            // --fi-stats-card-color: #ffffff;       
            // background-color:rgba(66, 133, 244);
            // color:white;
            // box-shadow:0 3px 8px rgba(176,196,222);
            // border-radius:12px;',
            //         'class' => '[&_.fi-wi-stats-overview-stat-label]:text-white',
            //     ]),

            Stat::make('Target Result', Result::count())
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah keseluruhan target result terdaftar')
                ->color(Color::hex('#ffffff'))  // atau Color::rgb(52, 168, 83)
                ->extraAttributes([
                    'style' => '
            background-color:rgba(66, 133, 244);
            box-shadow: 0 3px 8px rgba(176,196,222);
            border-radius: 12px;
        ',  // background sudah di-handle ->color(), jadi hapus background-color di sini
                ]),

            // ->extraAttributes([
            //     'class' => '[&_*]:!text-white',
            // ])





            Stat::make(' Akun Suspect', Result::distinct()->count('target_account'))
                // Stat::make('Akun Suspect', cache()->remember('result_count', 60, fn() => Result::count('target_account')))
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah keseluruhan Akun Suspect terdaftar ')
                ->color('white')
                ->extraAttributes([
                    'style' => '
                background-color:rgba(52, 168, 83);
                color:white;
                box-shadow:0 3px 8px rgba(176,196,222);
                border-radius:12px;',
                ]),


            Stat::make(' Keyword', Result::distinct()->count('keyword'))
                //Stat::make('Keyword', cache()->remember('resut_count', 60, fn() => Result::count('keyword')))
                ->icon('heroicon-o-squares-plus')
                ->description('Jumlah keseluruhan keyword terdaftar ')
                ->color('white')
                ->extraAttributes([
                    'style' => '
                background-color:rgba(220, 38, 38, 1);
                color:white;
                box-shadow:0 3px 8px rgba(176,196,222);
                border-radius:12px;',
                ]),

        ];
    }


    
}
