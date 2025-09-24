<?php

namespace App\Policies;

use App\Models\Statuspegawai;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StatusPegawaiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_status::pegawai');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Statuspegawai $statuspegawai): bool
    {
        return $user->can('view_status::pegawai');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_status::pegawai');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Statuspegawai $statuspegawai): bool
    {
        return $user->can('update_status::pegawai');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Statuspegawai $statuspegawai): bool
    {
        return $user->can('delete_status::pegawai');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Statuspegawai $statuspegawai): bool
    {
        return $user->can('restore_status::pegawai');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Statuspegawai $statuspegawai): bool
    {
        return $user->can('force_delete_status::pegawai');
    }
}
