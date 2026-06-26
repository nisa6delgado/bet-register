<?php

namespace App\Filament\Widgets;

use App\Models\Bet;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class BetsTable extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Bet::query())
            ->heading('Apuestas')
            ->columns([
                TextColumn::make('#')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration + ($livewire->getTableRecordsPerPage() * ($livewire->getTablePage() - 1))
                        );
                    }
                ),

                TextColumn::make('created_at')
                    ->label('Fecha y hora')
                    ->datetime('d/m/Y h:iA'),

                TextColumn::make('description')
                    ->label('Descripción')
                    ->formatStateUsing(fn (string $state): string => nl2br(e($state)))
                    ->html(),

                TextColumn::make('amount')
                    ->label('Monto')
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 2, ',', '.');
                    }),

                TextColumn::make('odds')
                    ->label('Cuota'),

                TextColumn::make('revenue')
                    ->label('Ganancia')
                    ->getStateUsing(function ($record) {
                        if ($record->result == 'Ganado') {
                            return number_format($record->amount * $record->odds, 2);
                        }
                    }),

                BadgeColumn::make('result')
                    ->label('Resultado')
                    ->colors([
                        'success' => 'Ganado',
                        'danger' => 'Perdido',
                        'info' => 'Nulo',
                        'primary' => 'Abierto',
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Editar')
                    ->button()
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
