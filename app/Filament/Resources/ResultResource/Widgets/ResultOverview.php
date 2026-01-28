<?php

namespace App\Filament\Resources\ResultResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Result;


class ResultOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            //Stat::make('Target Result', fn() => Result::count())
            Stat::make('Result', cache()->remember('result_count', 60, fn() => Result::count()))
                ->icon('heroicon-o-globe-alt')
                ->description('Jumlah keseluruhan result yang tersimpan')
                ->color('info'),
            //Stat::make(' Akun Suspect', Result::distinct()->count('target_account'))
            Stat::make('Akun Suspect', cache()->remember('result_count', 60, fn() => Result::count('target_account')))
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah keseluruhan Akun Suspect terdaftar ')
                ->color('info'),

            //Stat::make(' Keyword', Result::distinct()->count('keyword'))
            Stat::make('Keyword', cache()->remember('resut_count', 60, fn() => Result::count('keyword')))
                ->icon('heroicon-o-squares-plus')
                ->description('Jumlah keseluruhan keyword terdaftar ')
                ->color('info'),

        ];
    }
}
