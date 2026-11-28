<?php

declare(strict_types=1);

final class CartAdminController extends Controller
{
    private Cart $carts;

    public function __construct()
    {
        require_admin();
        $this->carts = new Cart();
    }

    public function index(): void
    {
        $keyword = ShopInput::keyword();
        $this->view('admin/carts/index', ShopView::admin('carts', 'Quản lý giỏ hàng') + [
            'pagination' => $this->carts->paginate($keyword, ShopInput::page()),
            'keyword' => $keyword,
        ], 'admin');
    }

    public function show(int|string $id): void
    {
        $userId = ShopInput::integer($id);
        $cart = $userId === null ? null : $this->carts->findByUser($userId);
        if ($cart === null) {
            (new ErrorController())->show('Không tìm thấy giỏ hàng.', 404);
            return;
        }
        $items = $this->carts->items($userId);
        $reviewKey = $items ? Cart::checkoutKey($items) : null;

        $this->view('admin/carts/show', ShopView::admin('carts', 'Chi tiết giỏ hàng') + [
            'userId' => $userId,
            'cart' => $cart,
            'items' => $items,
            'subtotal' => Cart::subtotal($items),
            'reviewKey' => $reviewKey,
            'reviewed' => $reviewKey !== null && $cart['reviewed_key'] === $reviewKey,
        ], 'admin');
    }

    public function review(int|string $id): void
    {
        $userId = ShopInput::integer($id);
        if ($userId === null) {
            set_flash('error', 'Không tìm thấy giỏ hàng.');
            $this->redirect('/admin/carts');
        }
        $path = '/admin/carts/' . $userId;
        if (!ShopInput::csrfValid()) {
            set_flash('error', 'Phiên gửi biểu mẫu đã hết hạn. Vui lòng thử lại.');
            $this->redirect($path);
        }

        try {
            $this->carts->markReviewed($userId, ShopInput::text($_POST, 'review_key'));
            set_flash('success', 'Đã đánh dấu giỏ hàng là đã xem.');
        } catch (DomainException $error) {
            set_flash('error', $error->getMessage());
        }
        $this->redirect($path);
    }
}
