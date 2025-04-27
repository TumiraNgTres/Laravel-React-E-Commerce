<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enum\ProductVariationTypeEnum;
use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class ProductVariationTypes extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected static ?string $navigationIcon = 'heroicon-s-square-3-stack-3d';

    protected static ?string $title = 'Variation Types';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Repeater::make('variationTypes') //  name must be equal to relationship to populate the data here in repeaters - to have connection with table
                    ->label(false)
                    ->relationship()
                    ->collapsible()
                    ->defaultItems(1)
                    ->addActionLabel('Add new variation type')
                    ->columns(2)
                    ->columnSpan(2)
                    ->schema([

                        TextInput::make('name')
                            ->required(),

                        Select::make('type')
                            ->options(ProductVariationTypeEnum::labels())
                            ->required(),

                        Repeater::make('options')
                            ->relationship()
                            ->collapsible()
                            ->columnSpan(2)
                            ->schema([
                                TextInput::make('name')
                                    ->columnSpan(2)
                                    ->required(),

                                SpatieMediaLibraryFileUpload::make('images')
                                    ->multiple()
                                    ->openable()
                                    ->panelLayout('grid')
                                    ->collection('images')
                                    ->reorderable()
                                    ->appendFiles()
                                    ->preserveFilenames()
                                    ->columnSpan(3)
                            ])

                    ])
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
