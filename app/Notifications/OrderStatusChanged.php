<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Order;

class OrderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Order $order, $oldStatus = null, $newStatus = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus ?? $order->status;
        $this->newStatus = $newStatus ?? $order->status;
    }

    /**
     * Kênh gửi thông báo
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Gửi email bằng Markdown view
     */
    public function toMail($notifiable)
    {
        $order = $this->order;

        // Map tên trạng thái thân thiện
        $labels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy',
            'failed' => 'Thất bại',
        ];

        $old = $labels[$this->oldStatus] ?? ucfirst($this->oldStatus);
        $new = $labels[$this->newStatus] ?? ucfirst($this->newStatus);

        return (new MailMessage)
            ->subject("SneakerUp | Cập nhật đơn hàng #{$order->code}")
            ->markdown('mail.orders.status', [
                'order' => $order,
                'oldStatus' => $old,
                'newStatus' => $new,
                'notifiable' => $notifiable,
            ]);
    }

    /**
     * Lưu thông báo trong database
     */
    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_code' => $this->order->code,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Đơn hàng #{$this->order->code} đã chuyển từ '{$this->oldStatus}' sang '{$this->newStatus}'.",
        ];
    }

    /**
     * (Tuỳ chọn) Gửi realtime
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'order_code' => $this->order->code,
            'new_status' => $this->newStatus,
        ]);
    }
}
