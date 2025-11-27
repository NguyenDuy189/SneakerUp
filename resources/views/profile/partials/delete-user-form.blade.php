{{--
    File này dùng Alpine.js (x-data, x-on...)
    Chúng ta chỉ dịch phần chữ, giữ nguyên code
--}}
<section class="space-y-6">
    <header>
        <h2>
            Xóa Tài Khoản
        </h2>

        <p>
            Sau khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu của nó sẽ bị xóa vĩnh viễn. Trước khi xóa tài khoản, vui lòng tải xuống mọi dữ liệu hoặc thông tin mà bạn muốn giữ lại.
        </p>
    </header>

    <button
        class="btn btn-danger"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >Xóa Tài Khoản</button>

    <div
        x-data="{ show: false }"
        x-show="show"
        x-on:open-modal.window="if ($event.detail === 'confirm-user-deletion') show = true"
        x-on:close.stop="show = false"
        style="display: none;"
        class="modal-backdrop"
    >
        <div @click.outside="show = false" class="modal-content">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <h2>
                    Bạn có chắc chắn muốn xóa tài khoản của mình không?
                </h2>

                <p>
                    Sau khi tài khoản của bạn bị xóa, tất cả tài nguyên và dữ liệu của nó sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu của bạn để xác nhận bạn muốn xóa vĩnh viễn tài khoản của mình.
                </p>

                <div>
                    <label for="password_modal" class="sr-only">Mật khẩu</label>
                    <input
                        id="password_modal"
                        name="password"
                        type="password"
                        placeholder="Mật khẩu"
                    >
                    @error('password', 'userDeletion')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 15px;">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="show = false"
                    >
                        Hủy
                    </button>

                    <button type="submit" class="btn btn-danger">
                        Xóa Tài Khoản
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Thêm CSS cơ bản cho Modal vào file CSS của bạn --}}
    <style>
        .modal-backdrop { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; }
        .modal-content { background-color: white; padding: 30px; border-radius: 8px; max-width: 500px; width: 100%; }
    </style>
</section>
