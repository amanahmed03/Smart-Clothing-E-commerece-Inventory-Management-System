<?php include APP_ROOT . "/app/Views/customer/customer_header.php"; ?>

<div style="margin-bottom: 28px;">
    <h1 style="font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 700; margin-bottom: 6px;">
        My Shopping Cart
    </h1>
    <p style="color: var(--text-secondary); font-size: 15px;">
        Review your selected smart apparel, modify quantities, or proceed to future checkout.
    </p>
</div>

<?php if (empty($cartItems)): ?>
    <div class="card" style="text-align: center; padding: 56px 20px;">
        <div style="font-size: 48px; margin-bottom: 16px;">🛒</div>
        <h2 style="font-family: 'Outfit', sans-serif; font-size: 22px; margin-bottom: 8px;">
            Your shopping cart is empty
        </h2>
        <p style="color: var(--text-secondary); font-size: 14px; max-width: 440px; margin: 0 auto 24px;">
            You have not added any smart apparel items to your cart yet. Explore our high-tech catalog to discover next-gen clothing.
        </p>
        <a href="<?= base_url('customer/products.php') ?>" class="btn btn-primary" style="padding: 12px 28px;">
            Start Shopping
        </a>
    </div>
<?php else: ?>
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; align-items: flex-start;">
        <!-- Cart Items Table / List -->
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color); background: rgba(15, 23, 42, 0.4);">
                            <th style="padding: 16px 20px; color: var(--text-secondary); font-weight: 600;">Product</th>
                            <th style="padding: 16px 14px; color: var(--text-secondary); font-weight: 600;">Price</th>
                            <th style="padding: 16px 14px; color: var(--text-secondary); font-weight: 600;">Quantity</th>
                            <th style="padding: 16px 14px; color: var(--text-secondary); font-weight: 600;">Subtotal</th>
                            <th style="padding: 16px 20px; text-align: right; color: var(--text-secondary); font-weight: 600;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <?php $subtotal = $item['price'] * $item['quantity']; ?>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <!-- Product Name & Size -->
                                <td style="padding: 18px 20px;">
                                    <div style="font-size: 11px; color: #818cf8; text-transform: uppercase; font-weight: 600;">
                                        <?= e($item['category_name']) ?>
                                    </div>
                                    <a href="<?= base_url('customer/product_details.php?id=' . $item['product_id']) ?>" style="color: #ffffff; font-weight: 600; text-decoration: none; font-size: 15px;">
                                        <?= e($item['product_name']) ?>
                                    </a>
                                    <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">
                                        Size: <span style="color: #cbd5e1; font-weight: 600;"><?= e($item['selected_size']) ?></span> &bull;
                                        Stock: <span style="color: <?= $item['stock_quantity'] > 0 ? 'var(--success)' : 'var(--danger)' ?>;"><?= (int)$item['stock_quantity'] ?></span>
                                    </div>
                                </td>

                                <!-- Price -->
                                <td style="padding: 18px 14px; font-weight: 500; white-space: nowrap;">
                                    $<?= number_format($item['price'], 2) ?>
                                </td>

                                <!-- Quantity Form -->
                                <td style="padding: 18px 14px; white-space: nowrap;">
                                    <form method="POST" action="<?= base_url('customer/update_cart.php') ?>" style="display: flex; align-items: center; gap: 8px;">
                                        <input type="hidden" name="cart_item_id" value="<?= $item['cart_item_id'] ?>">
                                        <input 
                                            type="number" 
                                            name="quantity" 
                                            value="<?= (int)$item['quantity'] ?>" 
                                            min="1" 
                                            max="<?= max(1, (int)$item['stock_quantity']) ?>" 
                                            style="width: 65px; padding: 6px 8px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 6px; color: #fff; font-size: 13px; text-align: center;"
                                        >
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Update quantity">
                                            Update
                                        </button>
                                    </form>
                                </td>

                                <!-- Subtotal -->
                                <td style="padding: 18px 14px; font-weight: 700; color: #ffffff; white-space: nowrap;">
                                    $<?= number_format($subtotal, 2) ?>
                                </td>

                                <!-- Remove Action -->
                                <td style="padding: 18px 20px; text-align: right; white-space: nowrap;">
                                    <a 
                                        href="<?= base_url('customer/remove_from_cart.php?id=' . $item['cart_item_id']) ?>" 
                                        class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Remove <?= htmlspecialchars($item['product_name'], ENT_QUOTES) ?> from your cart?');"
                                    >
                                        Remove
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="padding: 16px 20px; background: rgba(15, 23, 42, 0.4); display: flex; justify-content: space-between; align-items: center;">
                <a href="<?= base_url('customer/products.php') ?>" class="btn btn-secondary btn-sm">
                    &larr; Continue Shopping
                </a>
                <span style="font-size: 13px; color: var(--text-secondary);">
                    Total Items: <strong><?= $totalQuantity ?></strong>
                </span>
            </div>
        </div>

        <!-- Order Summary Card -->
        <div class="card" style="padding: 24px;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                Cart Summary
            </h3>

            <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--text-secondary); margin-bottom: 10px;">
                <span>Total Items</span>
                <span style="color: #fff; font-weight: 600;"><?= $totalQuantity ?></span>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--text-secondary); margin-bottom: 10px;">
                <span>Subtotal</span>
                <span style="color: #fff; font-weight: 600;">$<?= number_format($totalAmount, 2) ?></span>
            </div>

            <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--text-secondary); margin-bottom: 16px;">
                <span>Shipping Estimate</span>
                <span style="color: var(--success); font-weight: 600;">Calculated at checkout</span>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: baseline;">
                <span style="font-size: 16px; font-weight: 600;">Estimated Total</span>
                <span style="font-size: 24px; font-weight: 700; color: #ffffff;">
                    $<?= number_format($totalAmount, 2) ?>
                </span>
            </div>

<div style="padding: 12px; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 8px; font-size: 13px; color: #cbd5e1; margin-bottom: 16px; text-align: center;">
             🔒 Secure checkout - your payment information is protected
         </div>

             <a href="<?= base_url('customer/checkout.php') ?>" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;">
                 Proceed to Checkout
             </a>
         </div>
     </div>
<?php endif; ?>


<?php include APP_ROOT . "/app/Views/customer/customer_footer.php"; ?>