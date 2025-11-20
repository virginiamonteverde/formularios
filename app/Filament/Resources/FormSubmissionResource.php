<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormSubmissionResource\Pages;
use App\Models\FormSubmission;
use Filament\Forms;
use Filament\Forms\Form as FormForm;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationLabel = 'Envíos de formularios';
    protected static ?string $modelLabel = 'Envío';
    protected static ?string $pluralModelLabel = 'Envíos';

    public static function form(FormForm $form): FormForm
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('ID')
                    ->disabled(),

                Forms\Components\Placeholder::make('form_name')
                    ->label('Formulario')
                    ->content(fn (FormSubmission $record) => $record->form?->title ?? '—'),

                Forms\Components\KeyValue::make('data')
                    ->label('Datos enviados')
                    ->disableAddingRows()
                    ->disableDeletingRows()
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('form.title')
                    ->label('Formulario')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormSubmissions::route('/'),
            // Usamos sólo la página index; el ViewAction abre un modal con el schema de arriba
        ];
    }
}
