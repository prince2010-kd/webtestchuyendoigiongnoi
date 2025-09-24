<?php

namespace App\Policies;

use App\Models\User;
use App\Models\NhomQuyen;

class NhomQuyenPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermission('nhomquyen', 'can_view');
    }

    public function create(User $user)
    {
        return $user->hasPermission('nhomquyen', 'can_add');
    }

    public function update(User $user, NhomQuyen $nhomQuyen)
    {
        return $user->hasPermission('nhomquyen', 'can_edit');
    }

    public function delete(User $user, NhomQuyen $nhomQuyen)
    {
        return $user->hasPermission('nhomquyen', 'can_delete');
    }

    public function bulkDelete(User $user)
{
    return $user->hasPermission('nhomquyen', 'can_delete');
}
}
