<?php

namespace App\Policies;

use App\Models\OutgoingLetter;
use App\Models\User;
use App\Traits\UseEmployee;
use Illuminate\Auth\Access\Response;

class OutgoingLetterPolicy
{
    use UseEmployee;
    /**
     * Perform pre-authorization checks.
    */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole(['super-admin', 'operator-tu'])) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('outgouing-letters.index') || $this->isHeadOfDepartment($user->employee);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OutgoingLetter $outgoingLetter): bool
    {
        return $user->can('outgouing-letters.show');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('outgouing-letters.store');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OutgoingLetter $outgoingLetter): bool
    {
        return $user->can('outgouing-letters.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OutgoingLetter $outgoingLetter): bool
    {
        return $user->can('outgouing-letters.destroy');
    }
}
