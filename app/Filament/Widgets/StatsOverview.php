<?php

namespace App\Filament\Widgets;

use App\Models\Bet;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 4,
        ];
    }

    protected function getStats(): array
    {
        $wagared = 0;
        $earned = 0;
        $lost = 0;

        $bets = Bet::get();

        foreach ($bets as $bet) {
            $wagared += $bet->amount;

            if ($bet->result == 'Ganado') {
                $earned += ($bet->amount * $bet->odds) - $bet->amount;
            }

            if ($bet->result == 'Perdido') {
                $lost += $bet->amount;
            }
        }


        $balance = number_format($earned - $lost, 2, ',', '.');
        $wagared = number_format($wagared, 2, ',', '.');
        $earned = number_format($earned, 2, ',', '.');
        $lost = number_format($lost, 2, ',', '.');

        return [
            Stat::make('Dinero apostado', $wagared),
            Stat::make('Dinero ganado', $earned),
            Stat::make('Dinero perdido', $lost),
            Stat::make('Balance', $balance),

            Stat::make('Apuestas realizadas', Bet::count()),
        ];
    }
}
