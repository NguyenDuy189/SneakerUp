<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;

class PaymentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Payment $payment, $oldStatus, $newStatus)
    {
        $this->payment = $payment;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Cập nhật thanh toán #' . $this->payment->id)
            ->line("Trạng thái thanh toán đã chuyển từ **{$this->oldStatus}** sang **{$this->newStatus}**.")
            ->line('Số tiền: ' . number_format($this->payment->amount, 0, ',', '.') . ' ' . $this->payment->currency)
            ->action('Xem chi tiết đơn hàng', url('/orders/' . $this->payment->order_id))
            ->line('Cảm ơn bạn đã mua sắm tại SneakerUp!');
    }

    public function toArray($notifiable)
    {
        return [
            'payment_id' => $this->payment->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }
}
