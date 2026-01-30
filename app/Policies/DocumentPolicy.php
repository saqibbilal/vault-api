<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Perform pre-authorization checks.
     * If a user is a Super Admin, they can do anything.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null; // Fall through to specific methods
    }

    public function view(User $user, Document $document): bool
    {
        // Only the owner can view
        return $user->id === $document->user_id;
    }

    public function update(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->id === $document->user_id;
    }
}
