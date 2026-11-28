<?php

declare(strict_types=1);

final class OrderController extends Controller
{
    private int $userId;
    private Order $orders;

    public function __construct()
    {
        require_login();
        $this->userId = (int) current_user()['id'];
        $this->orders = new Order();
    }

    public function index(): void
    {
        $this->view('client/orders/index', ShopView::storefront('orders') + [
            'pageTitle' => 'Đơn hàng của tôi | Decor',
            'pagination' => $this->orders->paginate($this->userId, '', '', ShopInput::page()),
        ]);
    }

    public function show(int|string $id): void
    {
        $orderId = ShopInput::integer($id);
        $order = $orderId === null ? null : $this->orders->findById($orderId, $this->userId);
        if ($order === null) {
            (new ErrorController())->show('Không tìm thấy đơn hàng này.', 404);
            return;
        }

        $this->view('client/orders/show', ShopView::storefront('orders') + [
            'pageTitle' => $order['order_code'] . ' | Decor',
            'order' => $order,
            'items' => $this->orders->items($orderId),
        ]);
    }
}
