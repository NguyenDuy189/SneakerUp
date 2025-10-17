@component('mail::message')
# Cập nhật trạng thái đơn hàng {{ $order->code }}

Xin chào **{{ $notifiable->fullname }}**,  
Đơn hàng của bạn đã được cập nhật trạng thái.

**Trước đó:** {{ $oldStatus }}  
**Hiện tại:** {{ $newStatus }}

@component('mail::button', ['url' => url('/orders/' . $order->id)])
Xem chi tiết đơn hàng
@endcomponent

Cảm ơn bạn đã tin tưởng SneakerUp 💙  
— Đội ngũ SneakerUp
@endcomponent
