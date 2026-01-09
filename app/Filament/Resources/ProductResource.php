<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\SelectFilter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->description('Lengkapi detail sepatu di bawah ini.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Sepatu')
                            ->required()
                            ->maxLength(255),

                        // INI TEMPAT NGISH BRAND-NYA BRO
                        Select::make('brand')
                            ->label('Merk / Brand')
                            ->options([
                                'Nike' => 'Nike',
                                'Adidas' => 'Adidas',
                                'Puma' => 'Puma',
                                'Vans' => 'Vans',
                                'Ortuseight' => 'Ortuseight',
                                'Mills' => 'Mills',
                            ])
                            ->required()
                            ->searchable(),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('IDR')
                            ->required(),

                        TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->required(),

                        FileUpload::make('image')
                            ->label('Foto Produk')
                            ->image()
                            ->directory('products')
                            ->required(),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(5)
                            ->columnSpanFull()
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto'),

                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),

                // Kolom Brand di Tabel
                TextColumn::make('brand')
                    ->label('Merk')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable(),
            ])
            ->filters([
                // Tambahkan filter biar admin gampang nyari per merk
                SelectFilter::make('brand')
                    ->label('Saring per Brand')
                    ->options([
                                'Nike' => 'Nike',
                                'Adidas' => 'Adidas',
                                'Puma' => 'Puma',
                                'Vans' => 'Vans',
                                'Ortuseight' => 'Ortuseight',
                                'Mills' => 'Mills',
                    ]),

                SelectFilter::make('category_id')
                    ->label('Saring per Kategori')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
