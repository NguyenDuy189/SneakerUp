<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules(): array
    {
        $id = $this->route('brand')->id ?? null;
        return [
            'name' => 'required|string|max:191',
            'slug' => ['nullable','string','max:191', Rule::unique('brands','slug')->ignore($id)],
            'logo' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'country' => 'nullable|string|max:191',
            'is_active' => 'sometimes|boolean'
        ];
    }
}
