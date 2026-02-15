<?php

namespace App\Filament\Resources\DomainResource\Widgets;

use App\Models\Domain;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Result;
use App\Models\Tracer;
use PhpParser\Node\Stmt\Label;

class DomaiOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total = Result::count(); // <-- ambil data dari DB
        $totalTracer = Tracer::count();
        $totalPlatform = Tracer::distinct('type_platform')->count('type_platform');
        $totalKeyword = Result::distinct('keyword')->count('keyword');
        $totalAkun = Result::distinct('target_account')->count('target_account');



        return [
            Stat::make('Total URL', $total)
                ->icon('heroicon-o-globe-alt')
                ->description('Jumlah keseluruhan domain yang terdaftar')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            background-color:rgba(66, 133, 244);
            color:white;
            box-shadow:0 3px 8px rgba(176,196,222);
            border-radius:12px;',
                    'class' => '!bg-blue-600 !text-danger',
                ]),



            Stat::make('Total Platform', $totalPlatform)->icon('heroicon-o-globe-alt')
                ->Label('Tipe Platform')
                ->description('Jumlah keseluruhan Tipe Platform yang terdaftar')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            background-color:rgba(52, 168, 83);
            color:white;
            box-shadow:0 3px 8px rgba(176,196,222);
            border-radius:12px;',
                    'class' => '!bg-succes-600 !text-danger',
                ]),

            Stat::make('Total Keyword', $totalKeyword)->icon('heroicon-o-globe-alt')
                ->Label('Keyword')
                ->description('Jumlah keseluruhan keyword yang terdaftar')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            background-color:rgba(52, 168, 83);
            color:white;
            box-shadow:0 3px 8px rgba(176,196,222);
            border-radius:12px;',
                    'class' => '!bg-danger-600 !text-danger',
                ]),

            Stat::make('Total Target Akun', $totalAkun)->icon('heroicon-o-globe-alt')
                ->Label('Akun Suspect')
                ->description('Jumlah keseluruhan target akun yang terdaftar')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            background-color:rgba(52, 168, 83);
            color:white;
            box-shadow:0 3px 8px rgba(176,196,222);
            border-radius:12px;',
                    'class' => '!bg-warning-600 !text-danger',
                ]),



        ];
    }
}
