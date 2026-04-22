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
                ->extraAttributes([
                    'style' => '
           box-shadow: 0 -4px 6px -2px rgba(0, 0, 255, 0.6);
            border-radius:12px;',
                    'class' => '!bg-blue-600 !text-danger',
                ]),



            Stat::make('Total Platform', $totalPlatform)->icon('heroicon-o-globe-alt')
                ->Label('Tipe Platform')
                ->description('Jumlah keseluruhan Tipe Platform yang terdaftar')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            color:white;
            box-shadow: 0 -4px 6px -2px rgba(0, 128, 0, 0.4);            
            border-radius:12px;',
                    'class' => '!bg-succes-600 !text-danger',
                ]),

            Stat::make('Total Keyword', $totalKeyword)->icon('heroicon-o-globe-alt')
                ->Label('Keyword')
                ->description('Jumlah keseluruhan keyword yang terdaftar')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
           color:white;
            box-shadow: 0 -4px 6px -2px rgba(245, 158, 11, 0.4);
            border-radius:12px;',
                    'class' => '!bg-danger-600 !text-danger',
                ]),

            Stat::make('Total Target Akun', $totalAkun)
                ->icon('heroicon-o-globe-alt')
                ->label('Akun Suspect')
                ->description('Jumlah keseluruhan target akun yang terdaftar')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            color: white;
            box-shadow: 0 -4px 6px -2px rgba(255, 0, 0, 0.4);
            border-radius: 12px;',
                ]),



        ];
    }
}
