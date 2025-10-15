<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules(): array
    {
        $id = $this->route('category')->id ?? null;

        return [
            'name' => 'required|string|max:191',
            'slug' => ['nullable','string','max:191', Rule::unique('categories','slug')->ignore($id)],
            'parent_id' => 'nullable|exists:categories,id|not_in:'.$id,
            'image' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:4096',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ];
    }
}
