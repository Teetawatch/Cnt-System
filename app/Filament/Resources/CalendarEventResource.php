<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarEventResource\Pages;
use App\Filament\Resources\CalendarEventResource\RelationManagers;
use App\Models\CalendarEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CalendarEventResource extends Resource
{
    protected static ?string $model = CalendarEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'ระบบปฏิทิน';

    protected static ?string $navigationLabel = 'ปฏิทินกิจกรรม';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('staff_id')
                    ->relationship('staff', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('บุคลากร'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('ชื่อกิจกรรม/ภารกิจ'),
                Forms\Components\DatePicker::make('event_date')
                    ->required()
                    ->label('วันที่เริ่มต้น'),
                Forms\Components\DatePicker::make('end_date')
                    ->label('วันที่สิ้นสุด'),
                Forms\Components\TimePicker::make('start_time')
                    ->required()
                    ->label('เวลาเริ่มต้น'),
                Forms\Components\TimePicker::make('end_time')
                    ->label('เวลาสิ้นสุด'),
                Forms\Components\TextInput::make('location')
                    ->maxLength(255)
                    ->label('สถานที่'),
                Forms\Components\TextInput::make('organization')
                    ->maxLength(255)
                    ->label('หน่วยงาน'),
                Forms\Components\Textarea::make('description')
                    ->label('รายละเอียด')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'รอยืนยัน',
                        'confirmed' => 'ยืนยันแล้ว',
                        'cancelled' => 'ยกเลิก',
                    ])
                    ->required()
                    ->default('confirmed')
                    ->label('สถานะ'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('staff.name')
                    ->searchable()
                    ->sortable()
                    ->label('บุคลากร'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label('กิจกรรม'),
                Tables\Columns\TextColumn::make('event_date')
                    ->date('j F Y')
                    ->sortable()
                    ->label('วันที่'),
                Tables\Columns\TextColumn::make('start_time')
                    ->time('H:i')
                    ->label('เวลา'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'รอยืนยัน',
                        'confirmed' => 'ยืนยันแล้ว',
                        'cancelled' => 'ยกเลิก',
                        default => $state,
                    })
                    ->label('สถานะ'),
            ])
            ->defaultSort('event_date', 'desc')
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
            'index' => Pages\ListCalendarEvents::route('/'),
            'create' => Pages\CreateCalendarEvent::route('/create'),
            'edit' => Pages\EditCalendarEvent::route('/{record}/edit'),
        ];
    }
}
