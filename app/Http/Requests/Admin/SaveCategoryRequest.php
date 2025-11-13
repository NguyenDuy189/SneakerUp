<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; // Đã sửa ở bước trước

class SaveCategoryRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có được phép thực hiện request này hay không.
     */
    public function authorize(): bool
    {
        return Auth::check(); // Dùng Auth::check() cho rõ ràng
    }

    /**
     * Lấy các quy tắc validation áp dụng cho request.
     */
    public function rules(): array
    {
        $categoryId = $this->category ? $this->category->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($categoryId),
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$categoryId]), 
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ];
    }

    /**
     * === CẢI TIẾN: Thêm các thông báo tùy chỉnh ===
     * * Lấy các thông báo lỗi tùy chỉnh cho các quy tắc validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Các rule của 'name'
            'name.required' => 'Tên danh mục không được để trống.',
            'name.string'   => 'Tên danh mục phải là một chuỗi ký tự.',
            'name.max'      => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique'   => 'Tên danh mục này đã tồn tại. Vui lòng chọn tên khác.',

            // Các rule của 'parent_id'
            'parent_id.exists'  => 'Danh mục cha được chọn không hợp lệ.',
            'parent_id.not_in'  => 'Không thể chọn chính danh mục này làm danh mục cha.',

            // Các rule của 'status'
            'status.required' => 'Trạng thái không được để trống.',
            'status.in'       => 'Trạng thái được chọn không hợp lệ.',

            // Các rule của 'thumbnail_file'
            'thumbnail_file.image' => 'File tải lên phải là một hình ảnh.',
            'thumbnail_file.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, webp, gif.',
            'thumbnail_file.max'   => 'Hình ảnh không được vượt quá 2MB (2048KB).',
        ];
    }
}