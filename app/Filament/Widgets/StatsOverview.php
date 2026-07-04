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
        $winning = 0;
        $earned = 0;
        $losses = 0;
        $lost = 0;

        $bets = Bet::get();

        foreach ($bets as $bet) {
            $wagared += $bet->amount;

            if ($bet->result == 'Ganado') {
                $earned += ($bet->amount * $bet->odds) - $bet->amount;
                $winning += 1;
            }

            if ($bet->result == 'Perdido') {
                $losses += $bet->amount;
                $lost += 1;
            }
        }


        $balance = number_format($earned - $losses, 2, ',', '.');
        $wagared = number_format($wagared, 2, ',', '.');
        $earned = number_format($earned, 2, ',', '.');
        $losses = number_format($losses, 2, ',', '.');

        $total = count($bets);

        return [
            Stat::make('Dinero apostado', $wagared),
            Stat::make('Dinero ganado', $earned),
            Stat::make('Dinero perdido', $losses),
            Stat::make('Balance', $balance),

            Stat::make('Apuestas realizadas', $total),
            Stat::make('Apuestas ganadas', $winning),
            Stat::make('Apuestas perdidas', $lost),
        ];
    }
}
