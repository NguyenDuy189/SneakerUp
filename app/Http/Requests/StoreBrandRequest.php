<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:191',
            'slug' => 'nullable|string|max:191|unique:brands,slug',
            'logo' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'country' => 'nullable|string|max:191',
            'is_active' => 'sometimes|boolean'
        ];
    }
}
