<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductTestimonialResource\Pages;
use App\Filament\Resources\ProductTestimonialResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductTestimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductTestimonialResource extends Resource
{
    protected static ?string $model = ProductTestimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Product';

    public static function getNavigationBadge(): ?string
    {
        return (string) ProductTestimonial::where('is_publish', false)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(Product::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rating')
                    ->maxValue(5)
                    ->minValue(1)
                    ->placeholder('1 - 5')
                    ->required()
                    ->numeric(),
                Forms\Components\FileUpload::make('photo')
                    ->image()
                    ->disk('public')
                    ->directory('testimonials')
                    ->maxSize(2048)
                    ->placeholder('Upload a photo')
                    ->nullable(),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('customer_booking_trx_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_publish')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('product.thumbnail')
                ->label('Product Thumbnail')
                ->sortable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->description(fn ($record) => $record->customer_booking_trx_id),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_publish')
                    ->label('Publish ?')
                    ->boolean(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                    ])
                        ->dropdown(false),
                        Tables\Actions\Action::make('approve')
                        ->label('Approve')
                        ->action(function (ProductTestimonial $record) {
                            $record->is_publish = true;
                            $record->save();
    
                            Notification::make()
                                ->title('Testimonial Approved')
                                ->success()
                                ->body('The testimonial has been successfully approved.')
                                ->send();
                        })
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(ProductTestimonial $record) => !$record->is_publish),
                        Tables\Actions\DeleteAction::make()
                        ->visible(fn (ProductTestimonial $record) => $record->is_publish),
                ])
                    ->icon('heroicon-m-bars-3')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductTestimonials::route('/'),
            'create' => Pages\CreateProductTestimonial::route('/create'),
            'edit' => Pages\EditProductTestimonial::route('/{record}/edit'),
        ];
    }
}
