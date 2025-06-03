<?php

namespace App\Filament\Admin\Resources\PegawaiResource\Pages;

use App\Filament\Admin\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getState();

        if (isset($data['create_user_account']) && $data['create_user_account']) {
            // Validasi password dan konfirmasi
            if ($data['password'] !== $data['password_confirmation']) {
                throw ValidationException::withMessages([
                    'password_confirmation' => ['Konfirmasi kata sandi tidak cocok'],
                ]);
            }

            DB::transaction(function () use ($data) {
                // Bikin user baru
                $user = User::create([
                    'name' => $data['nm_pegawai'],
                    'username' => $data['nm_pegawai'],
                    'email' => $data['user_email'],
                    'password' => Hash::make($data['password']),
                ]);

                // Hubungkan ke pegawai
                $this->record->update(['user_id' => $user->id]);

                // Optional: Assign role kalo pake Filament Shield
                // $user->assignRole('pegawai');
            });
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
