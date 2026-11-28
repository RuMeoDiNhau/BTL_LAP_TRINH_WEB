<?php

declare(strict_types=1);

final class ProductAdminController extends Controller
{
    private Product $products;
    private Category $categories;

    public function __construct()
    {
        require_admin();
        $this->products = new Product();
        $this->categories = new Category();
    }

    public function index(): void
    {
        $keyword = ShopInput::keyword();
        $categoryId = ShopInput::integer($_GET['category'] ?? null) ?? 0;
        $status = ShopInput::text($_GET, 'status');
        if (!in_array($status, Product::STATUSES, true)) {
            $status = '';
        }

        $this->view('admin/products/index', ShopView::admin('products', 'Quản lý sản phẩm') + [
            'pagination' => $this->products->paginate($keyword, $categoryId, $status, ShopInput::page(), 12),
            'categories' => $this->categories->all(),
            'keyword' => $keyword,
            'categoryId' => $categoryId,
            'status' => $status,
        ], 'admin');
    }

    public function create(): void
    {
        $this->form(null);
    }

    public function edit(int|string $id): void
    {
        $product = $this->find($id);
        $this->form($product, $product + ['revision' => Product::revision($product)]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function update(int|string $id): void
    {
        $this->save($this->find($id));
    }

    public function delete(int|string $id): void
    {
        $this->requireToken('/admin/products');
        $product = $this->find($id);
        try {
            $result = $this->products->deleteOrHide((int) $product['id']);
            (new ProductImageUpload())->delete($result['thumbnail']);
            set_flash('success', $result['hidden']
                ? 'Đã ẩn sản phẩm vì sản phẩm có trong lịch sử đơn hàng.'
                : 'Đã xóa sản phẩm.');
        } catch (DomainException $error) {
            set_flash('error', $error->getMessage());
        }
        $this->redirect('/admin/products');
    }

    private function save(?array $product): void
    {
        $this->requireToken($product === null ? '/admin/products/create' : '/admin/products/' . $product['id'] . '/edit');
        $fields = ['name', 'category_id', 'description', 'price', 'sale_price', 'stock', 'status'];
        $values = [];
        foreach ($fields as $field) {
            $values[$field] = ShopInput::text($_POST, $field);
        }
        $values['thumbnail'] = $product['thumbnail'] ?? null;
        $values['revision'] = ShopInput::text($_POST, 'revision');

        $errors = [];
        $length = mb_strlen($values['name'], 'UTF-8');
        if ($length < 2 || $length > 200) {
            $errors['name'] = 'Nhập tên sản phẩm từ 2 đến 200 ký tự.';
        }
        $categoryId = ShopInput::integer($values['category_id']);
        if ($categoryId === null || !$this->categories->exists($categoryId)) {
            $errors['category_id'] = 'Chọn danh mục hợp lệ.';
        }
        if (mb_strlen($values['description'], 'UTF-8') > 10000) {
            $errors['description'] = 'Mô tả tối đa 10.000 ký tự.';
        }
        $price = ShopInput::integer($values['price'], 0);
        if ($price === null) {
            $errors['price'] = 'Nhập giá hợp lệ theo đơn vị đồng.';
        }
        $salePrice = ShopInput::integer($values['sale_price'], 0);
        if ($values['sale_price'] !== '' && ($salePrice === null || ($price !== null && $salePrice >= $price))) {
            $errors['sale_price'] = 'Giá ưu đãi cần nhỏ hơn giá gốc.';
        }
        $stock = ShopInput::integer($values['stock'], 0);
        if ($stock === null) {
            $errors['stock'] = 'Nhập số lượng tồn kho hợp lệ.';
        }
        if (!in_array($values['status'], Product::STATUSES, true)) {
            $errors['status'] = 'Chọn trạng thái hiển thị hợp lệ.';
        }
        if ($errors) {
            $this->form($product, $values, $errors);
            return;
        }

        $uploads = new ProductImageUpload();
        try {
            $thumbnail = $uploads->store($_FILES['thumbnail'] ?? []);
        } catch (InvalidArgumentException | RuntimeException $error) {
            $this->form($product, $values, ['thumbnail' => $error->getMessage()]);
            return;
        }

        $data = [
            'name' => $values['name'],
            'category_id' => $categoryId,
            'description' => $values['description'] === '' ? null : $values['description'],
            'price' => $price,
            'sale_price' => $salePrice,
            'stock' => $stock,
            'status' => $values['status'],
            'thumbnail' => $thumbnail,
        ];
        try {
            if ($product === null) {
                $this->products->create($data);
                set_flash('success', 'Đã thêm sản phẩm.');
            } else {
                $oldThumbnail = $this->products->update((int) $product['id'], $data, $values['revision']);
                $uploads->delete($oldThumbnail);
                set_flash('success', 'Đã cập nhật sản phẩm.');
            }
        } catch (DomainException $error) {
            $uploads->delete($thumbnail);
            set_flash('error', $error->getMessage());
        } catch (Throwable $error) {
            $uploads->delete($thumbnail);
            throw $error;
        }
        $this->redirect('/admin/products');
    }

    private function find(int|string $id): array
    {
        $productId = ShopInput::integer($id);
        $product = $productId === null ? null : $this->products->findById($productId);
        if ($product === null) {
            set_flash('error', 'Không tìm thấy sản phẩm.');
            $this->redirect('/admin/products');
        }

        return $product;
    }

    private function requireToken(string $path): void
    {
        if (!ShopInput::csrfValid()) {
            set_flash('error', 'Phiên gửi biểu mẫu đã hết hạn. Vui lòng thử lại.');
            $this->redirect($path);
        }
    }

    private function form(?array $product, array $values = [], array $errors = []): void
    {
        $title = $product === null ? 'Thêm sản phẩm' : 'Chỉnh sửa sản phẩm';
        if ($errors) {
            http_response_code(422);
        }

        $this->view('admin/products/form', ShopView::admin('products', $title) + [
            'title' => $title,
            'product' => $product,
            'categories' => $this->categories->all(),
            'values' => $values + [
                'name' => '', 'category_id' => '', 'description' => '', 'price' => '',
                'sale_price' => '', 'stock' => '0', 'status' => 'active', 'thumbnail' => null,
            ],
            'errors' => $errors,
        ], 'admin');
    }
}
