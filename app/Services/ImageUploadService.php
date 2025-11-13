<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Xử lý upload file, xóa file cũ (nếu có) và trả về đường dẫn public.
     *
     * @param UploadFile $file File được upload.
     * @param string $folder Thư mục lưu trong 'storage/app/public/'.
     * @param string|null $oldPath Đường dẫn file cũ (public URL) để xóa.
     * @return string Đường dẫn public mới (/storage/...).
     */
    public function handleUpload(UploadedFile $file, string $folder, ?string $oldPath = null): string
    {
        // 1. Xóa ảnh cũ nếu có
        if ($oldPath) {
            $oldDiskPath = Str::replace('/storage/', '', $oldPath);
            if (Storage::disk('public')->exists($oldDiskPath)) {
                Storage::disk('public')->delete($oldDiskPath);
            }
        }

        // 2. Tạo tên file mới
        $fileName = Str::random(20) . '.' . $file->getClientOriginalExtension();

        // 3. Lưu file mới
        $path = $file->storeAs($folder, $fileName, 'public');

        // 4. Trả về đường dẫn public
        return Storage::url($path);
    }
}