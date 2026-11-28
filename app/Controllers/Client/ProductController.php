<?php

declare(strict_types=1);

final class ProductController extends Controller
{
    public function index(): void
    {
        $keyword = ShopInput::keyword();
        $categoryId = ShopInput::integer($_GET['category'] ?? null) ?? 0;
        $products = (new Product())->paginate($keyword, $categoryId, 'active', ShopInput::page(), 9);

        $this->view('client/products/index', ShopView::storefront() + [
            'pageTitle' => 'Sản phẩm nội thất | Decor',
            'categories' => (new Category())->all(),
            'keyword' => $keyword,
            'categoryId' => $categoryId,
            'pagination' => $products,
        ]);
    }

    public function show(int|string $slug): void
    {
        $products = new Product();
        $product = $products->findActiveBySlug((string) $slug);
        if ($product === null) {
            (new ErrorController())->show('Không tìm thấy sản phẩm này.', 404);
            return;
        }

        $gallery = [];
        foreach ([$product['thumbnail'], ...$products->images((int) $product['id'])] as $path) {
            $image = ShopView::image($path);
            if ($image !== null) {
                $gallery[$image] = $image;
            }
        }

        $this->view('client/products/show', ShopView::storefront() + [
            'pageTitle' => $product['name'] . ' | Decor',
            'product' => $product,
            'gallery' => array_values($gallery),
        ]);
    }
}
