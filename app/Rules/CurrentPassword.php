<?php

namespace App\Rules;

use App\Models\Pengguna;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CurrentPassword implements Rule
{
    protected int $penggunaId;

    public function __construct(int $penggunaId)
    {
        $this->penggunaId = $penggunaId;
    }

    public function passes($attribute, $value): bool
    {
        $pengguna = Pengguna::find($this->penggunaId);
        return $pengguna && Hash::check($value, $pengguna->password);
    }

    public function message(): string
    {
        return 'Password saat ini tidak sesuai.';
    }
}
