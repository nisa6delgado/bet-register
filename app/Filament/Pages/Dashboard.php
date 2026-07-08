<?php

namespace App\Filament\Pages;

use App\Models\Bet;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
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
            Action::make('filter')
                ->label('Filtros')
                ->icon('heroicon-o-funnel')
                ->modalWidth(Width::Medium)
                ->modalSubmitActionLabel('Filtrar')
                ->form([
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('from')
                                ->label('Desde')
                                ->date()
                                ->required(),

                            DatePicker::make('until')
                                ->label('Hasta')
                                ->required(),
                        ])
                ])
                ->action(function ($data) {
                    return redirect('?from=' . $data['from'] . '&until=' . $data['until']);
                }),

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
