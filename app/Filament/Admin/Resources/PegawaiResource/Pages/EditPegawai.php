<?php

namespace App\Filament\Admin\Resources\PegawaiResource\Pages;

use App\Filament\Admin\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class EditPegawai extends EditRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function beforeSave(): void
    {
        $data = $this->form->getState();

        if (isset($data['create_user_account']) && $data['create_user_account']) {
            // Validasi password dan konfirmasi kalo password diisi
            if (isset($data['password']) && $data['password'] !== $data['password_confirmation']) {
                throw ValidationException::withMessages([
                    'password_confirmation' => ['Konfirmasi kata sandi tidak cocok'],
                ]);
            }
        }
    }

    protected function afterSave(): void
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data) {
            if (isset($data['create_user_account']) && $data['create_user_account']) {
                if ($this->record->user) {
                    // Update user existing
                    $userData = [
                        'name' => $data['nm_pegawai'],
                        'email' => $data['user_email'],
                    ];
                    if (isset($data['password']) && $data['password']) {
                        $userData['password'] = Hash::make($data['password']);
                    }
                    $this->record->user->update($userData);
                } else {
                    // Bikin user baru
                    $user = User::create([
                        'name' => $data['nm_pegawai'],
                        'email' => $data['user_email'],
                        'password' => Hash::make($data['password']),
                    ]);
                    $this->record->update(['user_id' => $user->id]);
                    // Optional: Assign role
                    // $user->assignRole('pegawai');
                }
            } elseif ($this->record->user && (!isset($data['create_user_account']) || !$data['create_user_account'])) {
                // Hapus user kalo toggle dimatiin
                $this->record->user->delete();
                $this->record->update(['user_id' => null]);
            }
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
