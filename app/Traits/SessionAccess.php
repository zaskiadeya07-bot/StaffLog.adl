<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait SessionAccess
{
    protected function getPenggunaId(Request|array|null $request = null): ?int
    {
        if ($request instanceof Request) {
            return $request->session()->get('pengguna_id');
        }
        return session('pengguna_id');
    }

    protected function getPenggunaRole(Request|array|null $request = null): ?string
    {
        if ($request instanceof Request) {
            return $request->session()->get('pengguna_role');
        }
        return session('pengguna_role');
    }

    protected function sessionHasPengguna(Request|array|null $request = null): bool
    {
        if ($request instanceof Request) {
            return $request->session()->has('pengguna_id');
        }
        return session()->has('pengguna_id');
    }
}
