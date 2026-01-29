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
                ->color('white')
                ->extraAttributes([
                    'style' => '
            --fi-stats-card-color: #ffffff;       
            background-color:rgba(66, 133, 244);
            color:white;
            box-shadow:0 8px 15px rgba(176,196,222);
            border-radius:12px;',
                    'class' => '!bg-blue-600 !text-danger',
                ]),

            Stat::make(
                ' Email',
                User::distinct()->count('email')

            )
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah email terdaftar ')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            background-color:rgba(52, 168, 83);
            color:white;
            box-shadow:0 8px 15px rgba(176,196,222);
            border-radius:12px;',
                ]),



            Stat::make(
                ' Role',
                User::distinct()->count('role')

            )
                ->icon('heroicon-o-square-3-stack-3d')
                ->description('Jumlah role terdaftar ')
                ->color('white')
                ->extraAttributes([
                    'style' => '
            background-color:rgba(234, 67, 53);
            color:white;
            box-shadow:0 8px 15px rgba(176,196,222);
            border-radius:12px;',
                ]),

        ];
    }
}
