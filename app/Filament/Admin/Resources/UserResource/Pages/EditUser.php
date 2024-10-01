<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount(int | string $record): void
    {
        if (!auth()->user()->hasRole('superadmin') && $this->record == 1) {
            abort(403);
        }
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->fillForm();

        $this->previousUrl = url()->previous();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!$data['password']) {
            unset($data['password']);
        }

        if (auth()->user()->hasRole(['superadmin'])) {
            $data['email_verified_at'] = now();
        }

        $data['username'] = \Str::slug($data['username']);

        return $data;
    }
}
