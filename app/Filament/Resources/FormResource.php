<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormResource\Pages;
use App\Models\Form;
use Filament\Forms;
use Filament\Forms\Form as FormForm;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Formularios';
    protected static ?string $modelLabel = 'Formulario';
    protected static ?string $pluralModelLabel = 'Formularios';

    public static function form(FormForm $form): FormForm
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required(),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug (URL pública)')
                    ->unique(ignoreRecord: true)
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3),

                Forms\Components\Toggle::make('is_published')
                    ->label('Publicado'),

                Forms\Components\Repeater::make('schema')
                    ->label('Campos del formulario')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tipo de campo')
                            ->options([
                                'text'     => 'Texto',
                                'email'    => 'Email',
                                'textarea' => 'Área de texto',
                                'select'   => 'Lista desplegable',
                                'checkbox' => 'Checkbox (sí/no)',
                            ])
                            ->required()
                            ->live(),   // 🔹 IMPORTANTE: hace reactivo el campo

                        Forms\Components\TextInput::make('label')
                            ->label('Etiqueta visible')
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre interno (name)')
                            ->helperText('Sin espacios, sin acentos. Ej: nombre_completo')
                            ->required(),

                        Forms\Components\Toggle::make('required')
                            ->label('Obligatorio')
                            ->default(false),

                        // Opciones solo para campos select
                        Forms\Components\KeyValue::make('options')
                            ->label('Opciones (para select)')
                            ->helperText('Clave = valor enviado, Valor = texto visible')
                            ->visible(fn (Get $get) => $get('type') === 'select'),
                    ])
                    ->addActionLabel('Agregar campo')
                    ->reorderable(true)   // permite drag & drop
                    ->cloneable()         // duplicar campos
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug'),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Publicado'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->label('Creado'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListForms::route('/'),
            'create' => Pages\CreateForm::route('/create'),
            'edit'   => Pages\EditForm::route('/{record}/edit'),
        ];
    }
}
