<div class="shop-table-scroll" tabindex="0" role="region" aria-label="Sản phẩm trong đơn">
  <table class="shop-table">
    <thead>
      <tr><th scope="col">Sản phẩm</th><th scope="col">Đơn giá</th><th scope="col">Số lượng</th><th scope="col">Thành tiền</th></tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <th scope="row"><?= e($item['product_name']) ?></th>
          <td class="shop-nowrap"><?= ShopView::money((int) $item['price']) ?></td>
          <td><?= (int) $item['quantity'] ?></td>
          <td class="shop-nowrap"><?= ShopView::money((int) $item['price'] * (int) $item['quantity']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot><tr><th scope="row" colspan="3">Tổng đơn hàng</th><td class="shop-nowrap"><strong><?= ShopView::money((int) $order['total']) ?></strong></td></tr></tfoot>
  </table>
</div>
