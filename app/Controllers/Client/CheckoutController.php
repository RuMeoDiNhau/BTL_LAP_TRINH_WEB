<?php

declare(strict_types=1);

final class CheckoutController extends Controller
{
    private int $userId;

    public function __construct()
    {
        require_login();
        $this->userId = (int) current_user()['id'];
    }

    public function index(): void
    {
        $user = (new User())->findById($this->userId);
        $this->show([
            'receiver_name' => $user['full_name'] ?? '',
            'phone' => $user['phone'] ?? '',
            'address' => '',
            'note' => '',
        ]);
    }

    public function store(): void
    {
        if (!ShopInput::csrfValid()) {
            set_flash('error', 'Phiên gửi biểu mẫu đã hết hạn. Vui lòng thử lại.');
            $this->redirect('/checkout');
        }

        $delivery = [
            'receiver_name' => ShopInput::text($_POST, 'receiver_name'),
            'phone' => ShopInput::text($_POST, 'phone'),
            'address' => ShopInput::text($_POST, 'address'),
            'note' => ShopInput::text($_POST, 'note'),
        ];
        $errors = [];
        $nameLength = mb_strlen($delivery['receiver_name'], 'UTF-8');
        if ($nameLength < 2 || $nameLength > 100) {
            $errors['receiver_name'] = 'Nhập tên người nhận từ 2 đến 100 ký tự.';
        }
        $phone = preg_replace('/[ ().-]+/', '', $delivery['phone']);
        if (strlen($delivery['phone']) > 20 || !preg_match('/^\+?[0-9]{8,15}$/D', $phone)) {
            $errors['phone'] = 'Nhập số điện thoại hợp lệ, từ 8 đến 15 chữ số.';
        }
        $addressLength = mb_strlen($delivery['address'], 'UTF-8');
        if ($addressLength < 5 || $addressLength > 255) {
            $errors['address'] = 'Nhập địa chỉ giao hàng từ 5 đến 255 ký tự.';
        }
        if (mb_strlen($delivery['note'], 'UTF-8') > 255) {
            $errors['note'] = 'Ghi chú tối đa 255 ký tự.';
        }
        if ($errors) {
            http_response_code(422);
            $this->show($delivery, $errors);
            return;
        }

        $delivery['phone'] = $phone;
        try {
            $id = (new Order())->place($this->userId, $delivery, ShopInput::text($_POST, 'cart_key'));
        } catch (DomainException $error) {
            set_flash('error', $error->getMessage());
            $this->redirect('/cart');
        }

        set_flash('success', 'Đặt hàng thành công. Cửa hàng sẽ xác nhận đơn của bạn.');
        $this->redirect('/orders/' . $id);
    }

    private function show(array $delivery, array $errors = []): void
    {
        $cart = new Cart();
        $items = $cart->items($this->userId);
        $total = Cart::subtotal($items);
        $problem = Cart::checkoutProblem($items, $total);
        if ($problem !== null) {
            set_flash('error', $problem);
            $this->redirect('/cart');
        }

        $this->view('client/cart/checkout', ShopView::storefront('cart') + [
            'pageTitle' => 'Đặt hàng | Decor',
            'delivery' => $delivery,
            'errors' => $errors,
            'items' => $items,
            'total' => $total,
            'checkoutKey' => Cart::checkoutKey($items),
        ]);
    }
}
