<?php

declare(strict_types=1);

final class CartController extends Controller
{
    private int $userId;
    private Cart $cart;

    public function __construct()
    {
        require_login();
        $this->userId = (int) current_user()['id'];
        $this->cart = new Cart();
    }

    public function index(): void
    {
        $items = $this->cart->items($this->userId);
        $subtotal = Cart::subtotal($items);

        $this->view('client/cart/index', ShopView::storefront('cart') + [
            'pageTitle' => 'Giỏ hàng | Decor',
            'items' => $items,
            'subtotal' => $subtotal,
            'checkoutProblem' => Cart::checkoutProblem($items, $subtotal),
        ]);
    }

    public function add(): void
    {
        $this->change('add', $_POST['product_id'] ?? null);
    }

    public function update(int|string $id): void
    {
        $this->change('update', $id);
    }

    public function remove(int|string $id): void
    {
        $this->change('remove', $id);
    }

    private function change(string $action, $productId): void
    {
        if (!ShopInput::csrfValid()) {
            set_flash('error', 'Phiên gửi biểu mẫu đã hết hạn. Vui lòng thử lại.');
            $this->redirect('/cart');
        }
        $id = ShopInput::integer($productId);
        $quantity = ShopInput::integer($_POST['quantity'] ?? null, 1, Cart::MAX_QUANTITY);
        if ($id === null || ($action !== 'remove' && $quantity === null)) {
            set_flash('error', 'Sản phẩm hoặc số lượng không hợp lệ.');
            $this->redirect('/cart');
        }

        try {
            switch ($action) {
                case 'add':
                    $this->cart->add($this->userId, $id, $quantity);
                    set_flash('success', 'Đã thêm sản phẩm vào giỏ.');
                    break;
                case 'update':
                    $this->cart->update($this->userId, $id, $quantity);
                    set_flash('success', 'Đã cập nhật số lượng.');
                    break;
                case 'remove':
                    $this->cart->remove($this->userId, $id);
                    set_flash('success', 'Đã xóa sản phẩm khỏi giỏ.');
                    break;
            }
        } catch (DomainException $error) {
            set_flash('error', $error->getMessage());
        }
        $this->redirect('/cart');
    }
}
