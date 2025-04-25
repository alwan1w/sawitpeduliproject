<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationGroup = 'Roles and Permissions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required(fn ($record) => $record === null), // Hanya wajib saat buat user baru
                Forms\Components\Select::make('role')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->required()
                    ->preload(),
            ]);
    }

    // public static function canAccess(): bool
    // {
    //     return Gate::allows('akses_users');
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable(),
                Tables\Columns\TextColumn::make('email')->sortable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Role'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function afterSave(User $record, array $data): void
    {
        if (isset($data['role'])) {
            // Sinkronisasi Role
            $record->syncRoles([$data['role']]);

            // Ambil semua permission dari role yang dipilih
            $role = Role::findByName($data['role']);
            $permissions = $role->permissions;

            // Sinkronisasi semua permission ke user
            $record->syncPermissions($permissions);
        }
    }
}
