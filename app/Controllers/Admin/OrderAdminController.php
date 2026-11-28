<?php

declare(strict_types=1);

final class OrderAdminController extends Controller
{
    private Order $orders;

    public function __construct()
    {
        require_admin();
        $this->orders = new Order();
    }

    public function index(): void
    {
        $keyword = ShopInput::keyword();
        $status = ShopInput::text($_GET, 'status');
        if (!isset(Order::STATUS_LABELS[$status])) {
            $status = '';
        }

        $this->view('admin/orders/index', ShopView::admin('orders', 'Quản lý đơn hàng') + [
            'pagination' => $this->orders->paginate(null, $keyword, $status, ShopInput::page()),
            'keyword' => $keyword,
            'status' => $status,
        ], 'admin');
    }

    public function show(int|string $id): void
    {
        $orderId = ShopInput::integer($id);
        $order = $orderId === null ? null : $this->orders->findById($orderId);
        if ($order === null) {
            (new ErrorController())->show('Không tìm thấy đơn hàng.', 404);
            return;
        }

        $this->view('admin/orders/show', ShopView::admin('orders', 'Chi tiết đơn hàng') + [
            'order' => $order,
            'items' => $this->orders->items($orderId),
            'nextStatuses' => Order::nextStatuses($order['status']),
        ], 'admin');
    }

    public function status(int|string $id): void
    {
        $orderId = ShopInput::integer($id);
        if ($orderId === null || $this->orders->findById($orderId) === null) {
            set_flash('error', 'Không tìm thấy đơn hàng.');
            $this->redirect('/admin/orders');
        }
        $path = '/admin/orders/' . $orderId;
        if (!ShopInput::csrfValid()) {
            set_flash('error', 'Phiên gửi biểu mẫu đã hết hạn. Vui lòng thử lại.');
            $this->redirect($path);
        }

        try {
            $changed = $this->orders->updateStatus($orderId, ShopInput::text($_POST, 'status'));
            set_flash('success', $changed ? 'Đã cập nhật trạng thái đơn hàng.' : 'Đơn hàng đã ở trạng thái này.');
        } catch (DomainException $error) {
            set_flash('error', $error->getMessage());
        }
        $this->redirect($path);
    }
}
