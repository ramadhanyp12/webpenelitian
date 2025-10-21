<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            // fields di tabel users
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],

            // fields di tabel profiles
            'phone'       => ['nullable', 'string', 'max:30'],
            'kampus'      => ['nullable', 'string', 'max:255'],
            'nim'         => ['nullable', 'string', 'max:50'],
            'prodi'       => ['nullable', 'string', 'max:255'],
            'konsentrasi' => ['nullable', 'string', 'max:255'],
        ];
    }
}
