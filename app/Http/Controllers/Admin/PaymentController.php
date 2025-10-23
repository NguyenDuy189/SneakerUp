<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Notifications\PaymentStatusChanged;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private array $allowedTransitions = [
        'pending' => ['paid', 'failed', 'cancelled'],
        'paid' => ['refunded'],
        'failed' => [],
        'refunded' => [],
        'cancelled' => [],
        'chargeback' => [],
    ];

    private array $statuses = [
        'pending' => 'Đang chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thanh toán thất bại',
        'refunded' => 'Đã hoàn tiền',
        'cancelled' => 'Đơn hàng bị hủy',
        'chargeback' => 'Khách kiện',
    ];

    private array $statusColors = [
        'pending' => 'warning',
        'paid' => 'success',
        'failed' => 'danger',
        'refunded' => 'secondary',
        'cancelled' => 'dark',
        'chargeback' => 'info',
    ];

    // 🔹 Danh sách thanh toán
    // 🔹 Danh sách thanh toán
public function index(Request $request)
{
    $query = Payment::query()
        ->with(['order.user', 'confirmer'])
        ->latest();

    // 🔍 Tìm kiếm
    if ($search = $request->get('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('payments.id', 'like', "%{$search}%")
            ->orWhere('payments.method', 'like', "%{$search}%")
            ->orWhereHas('order', function ($q2) use ($search) {
                $q2->where('code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q3) use ($search) {
                        $q3->where('fullname', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        });
    }

    // 🔖 Lọc theo trạng thái
    if ($status = $request->get('status')) {
        $query->where('status', $status);
    }

    $payments = $query->paginate(10)->withQueryString();

    return view('admin.payments.index', [
        'payments' => $payments,
        'statuses' => $this->statuses,
        'statusColors' => $this->statusColors,
        'search' => $search,
    ]);
}


    // 🔹 Chi tiết thanh toán
    public function show(Payment $payment)
    {
        $payment->load(['order', 'confirmer', 'order.user']);
        $nextStatuses = $this->allowedTransitions[$payment->status] ?? [];

        return view('admin.payments.show', [
            'payment' => $payment,
            'statuses' => $this->statuses,
            'statusColors' => $this->statusColors,
            'nextStatuses' => $nextStatuses,
        ]);
    }

    // 🔹 Cập nhật trạng thái thanh toán
    public function updateStatus(Request $request, Payment $payment)
    {
        $validated = $request->validate(['status' => 'required|string']);
        $old = $payment->status;
        $new = $validated['status'];
        $allowed = $this->allowedTransitions[$old] ?? [];
        $order = $payment->order;

        // --- Chặn trạng thái không hợp lệ ---
        if (!in_array($new, $allowed)) {
            return back()->with('error', 'Không thể chuyển từ "' . ($this->statuses[$old] ?? $old) . '" sang "' . ($this->statuses[$new] ?? $new) . '".');
        }

        // --- Logic nghiệp vụ bổ sung ---
        if ($new === 'refunded') {
            if (!$order || !in_array($order->status, ['cancelled', 'returned'])) {
                return back()->with('error', '❌ Chỉ có thể hoàn tiền khi đơn hàng đã bị hủy hoặc trả hàng.');
            }
            if ($payment->status !== 'paid') {
                return back()->with('error', '❌ Không thể hoàn tiền vì giao dịch chưa được thanh toán.');
            }
        }

        if ($new === 'paid' && $order && in_array($order->status, ['cancelled', 'failed'])) {
            return back()->with('error', '❌ Không thể đánh dấu thanh toán cho đơn hàng đã bị hủy hoặc thất bại.');
        }

        if ($old === $new) {
            return back()->with('info', 'Trạng thái thanh toán không thay đổi.');
        }

        DB::transaction(function () use ($payment, $new) {
            $payment->update(['status' => $new]);
            if ($new === 'refunded') {
                $this->handleRefund($payment);
            }
        });

        // 🔔 Gửi thông báo tới khách hàng
        if ($payment->order && $payment->order->user) {
            $payment->order->user->notify(new PaymentStatusChanged($payment, $old, $new));
        }

        return back()->with('success', 'Cập nhật trạng thái "' . ($this->statuses[$new] ?? $new) . '" thành công!');
    }

    // 🔹 Xử lý hoàn tiền & điểm thưởng
    protected function handleRefund(Payment $payment)
    {
        $order = $payment->order;
        $user = $order?->user;

        if ($payment->method === 'COD') {
            logger("COD - Không hoàn tự động cho #{$payment->id}");
            return;
        }

        if (in_array($payment->method, ['Momo', 'VNPay', 'PayPal'])) {
            try {
                $payment->update(['refund_reference' => 'REF-' . now()->timestamp]);
                logger("Hoàn tiền thành công cho Payment #{$payment->id}");
            } catch (\Exception $e) {
                $payment->update(['status' => 'failed']);
                throw $e;
            }
        }

        // Trừ điểm thưởng nếu đã cộng
        if ($user) {
            $points = intval($payment->amount / 1000);
            $user->decrement('points', $points);
            logger("Trừ {$points} điểm cho User #{$user->id} sau khi hoàn tiền Payment #{$payment->id}");
        }
    }
}
