<?php

namespace App\Policies;

use App\Models\IncomingLetter;
use App\Models\User;
use App\Traits\UseEmployee;
use Illuminate\Auth\Access\Response;

class IncomingLetterPolicy
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
        return $user->can('incoming-letters.index') || $this->isHeadOfDepartment($user->employee);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, IncomingLetter $incomingLetter): bool
    {
        return ($incomingLetter->to === $user->id && ($user->can('incoming-letters.show') || $user->can('incoming-letters.update'))) || $this->isHeadOfDepartment($user->employee);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('incoming-letters.store');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, IncomingLetter $incomingLetter): bool
    {
        return $user->can('incoming-letters.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, IncomingLetter $incomingLetter): bool
    {
        return $user->can('incoming-letters.update');
    }

    /**
     * retrive user can disposition
    */
    public function disposition(User $user): bool
    {
        return $user->can('incoming-letters.disposition') || $this->loginAsHeadOfDepartment();
    }
}
