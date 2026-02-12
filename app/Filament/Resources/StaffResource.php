<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffResource\Pages;
use App\Filament\Resources\StaffResource\RelationManagers;
use App\Models\Staff;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StaffResource extends Resource
{
    protected static ?string $model = Staff::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'ข้อมูลพื้นฐาน';

    protected static ?string $navigationLabel = 'บุคลากร';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('ชื่อ-นามสกุล'),
                Forms\Components\TextInput::make('position')
                    ->required()
                    ->maxLength(255)
                    ->label('ตำแหน่ง'),
                Forms\Components\TextInput::make('department')
                    ->required()
                    ->maxLength(255)
                    ->label('กลุ่มสาระ/ฝ่าย'),
                Forms\Components\Textarea::make('description')
                    ->label('รายละเอียดเพิ่มเติม')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('photo')
                    ->image()
                    ->directory('staff-photos')
                    ->label('รูปภาพ'),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true)
                    ->label('สถานะการใช้งาน'),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('ลำดับการแสดงผล'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('รูปภาพ'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('ชื่อ-นามสกุล'),
                Tables\Columns\TextColumn::make('position')
                    ->searchable()
                    ->label('ตำแหน่ง'),
                Tables\Columns\TextColumn::make('department')
                    ->searchable()
                    ->label('กลุ่มสาระ/ฝ่าย'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('สถานะ'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->label('ลำดับ'),
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaff::route('/'),
            'create' => Pages\CreateStaff::route('/create'),
            'edit' => Pages\EditStaff::route('/{record}/edit'),
        ];
    }
}
