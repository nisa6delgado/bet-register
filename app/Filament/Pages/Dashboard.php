<?php

namespace App\Filament\Pages;

use App\Models\Bet;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Grid;
use Filament\Support\Enums\Width;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Registrar apuesta')
                ->icon('heroicon-o-plus')
                ->modalWidth(Width::Medium)
                ->modalSubmitActionLabel('Registar apuesta')
                ->form([
                    Textarea::make('description')
                        ->label('Descripción')
                        ->required(),

                    Grid::make(2)
                        ->schema([
                            TextInput::make('amount')
                                ->label('Monto')
                                ->required(),

                            TextInput::make('odds')
                                ->label('Cuota')
                                ->required(),
                        ])
                ])
                ->action(function ($data) {
                    Bet::create([
                        'description' => $data['description'],
                        'amount' => $data['amount'],
                        'odds' => $data['odds'],
                        'result' => 'Abierto',
                    ]);

                    Notification::make()
                        ->title('Apuesta creada exitosamente')
                        ->success()
                        ->send();

                    return redirect('/');
                }),
        ];
    }
}
