<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(' User', User::count())
                ->icon('heroicon-o-globe-alt')
                ->description('Jumlah keseluruhan user')
                ->extraAttributes([
                    'style' => '        
            box-shadow: 0 -4px 6px -2px rgba(0, 0, 255, 0.6);
            border-radius:12px;',
                    'class' => '[&_.fi-wi-stats-overview-stat-label]:text-white',
                ]),

            Stat::make(
                ' Email',
                User::distinct()->count('email')

            )
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah email terdaftar ')
                ->extraAttributes([
                    'style' => '
            color:white;
            box-shadow: 0 -4px 6px -2px rgba(0, 128, 0, 0.4);            
                border-radius:12px;',
                ]),



            Stat::make(
                ' Role',
                User::distinct()->count('role')

            )
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah role terdaftar ')
                ->extraAttributes([
                    'style' => '
                color:white;
            box-shadow: 0 -4px 6px -2px rgba(255, 0, 0, 0.4);
                border-radius:12px;',
                ]),

        ];
    }
}
