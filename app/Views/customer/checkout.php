<?php include APP_ROOT . "/app/Views/customer/customer_header.php"; ?>

<div style="margin-bottom: 28px;">
    <h1 style="font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 700; margin-bottom: 6px;">
        Checkout
    </h1>
    <p style="color: var(--text-secondary); font-size: 15px;">
        Review your order and provide delivery details.
    </p>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; align-items: flex-start;">
    <form method="POST" action="checkout.php" novalidate>
        <!-- Checkout Form -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Cart Items Summary -->
            <div class="card" style="padding: 24px;">
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    Cart Items (<?= $totalQuantity ?>)
                </h3>
                <?php foreach ($cartItems as $item): ?>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color); font-size: 14px;">
                        <div>
                            <strong style="color: #fff;"><?= e($item['product_name']) ?></strong>
                            <div style="font-size: 12px; color: var(--text-muted);">Size: <?= e($item['selected_size']) ?></div>
                        </div>
                        <div style="text-align: right;">
                            <div style="color: #fff;">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                            <div style="font-size: 12px; color: var(--text-muted);">x<?= $item['quantity'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Shipping Information -->
            <div class="card" style="padding: 24px;">
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    Shipping Information
                </h3>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Full Name</label>
                        <input type="text" value="<?= e($profile['name']) ?>" disabled style="width: 100%; padding: 10px 14px; background: rgba(15, 23, 42, 0.5); border: 1px solid #1e293b; border-radius: 8px; color: var(--text-secondary); font-size: 14px;">
                    </div>
                    <div>
                        <label for="shipping_address" style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Shipping Address <span style="color: var(--danger);">*</span></label>
                        <textarea id="shipping_address" name="shipping_address" rows="3" required placeholder="Enter delivery address" style="width: 100%; padding: 10px 14px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: #fff; font-size: 14px; resize: vertical;"><?= e($_POST['shipping_address'] ?? $profile['address']) ?></textarea>
                    </div>
                    <div>
                        <label for="shipping_phone" style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Phone Number <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="shipping_phone" name="shipping_phone" value="<?= e($_POST['shipping_phone'] ?? $profile['phone']) ?>" required placeholder="e.g. +1-555-0199" style="width: 100%; padding: 10px 14px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: #fff; font-size: 14px;">
                    </div>
                    <div>
                        <label for="notes" style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px;">Order Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="2" placeholder="Any special instructions..." style="width: 100%; padding: 10px 14px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: #fff; font-size: 14px; resize: vertical;"><?= e($_POST['notes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="card" style="padding: 24px;">
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    Payment Method
                </h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 10px; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--border-color); border-radius: 8px;">
                        <input type="radio" name="payment_method" value="cash_on_delivery" checked style="width: auto;">
                        <span style="font-size: 14px; color: #fff;">Cash on Delivery</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 10px; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--border-color); border-radius: 8px;">
                        <input type="radio" name="payment_method" value="card" style="width: auto;">
                        <span style="font-size: 14px; color: #fff;">Card Payment</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 10px; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--border-color); border-radius: 8px;">
                        <input type="radio" name="payment_method" value="mobile_banking" style="width: auto;">
                        <span style="font-size: 14px; color: #fff;">Mobile Banking</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card" style="padding: 24px;">
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    Order Summary
                </h3>
                <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--text-secondary);">
                        <span>Items (<?= $totalQuantity ?>)</span>
                        <span style="color: #fff;">$<?= number_format($totalAmount, 2) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--text-secondary);">
                        <span>Shipping</span>
                        <span style="color: var(--success);">Free</span>
                    </div>
                    <div style="border-top: 1px solid var(--border-color); padding-top: 14px; display: flex; justify-content: space-between; align-items: baseline;">
                        <span style="font-size: 18px; font-weight: 700;">Total</span>
                        <span style="font-size: 28px; font-weight: 700; color: #fff;">$<?= number_format($totalAmount, 2) ?></span>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error" style="margin-bottom: 16px;">
                        <?php foreach ($errors as $err): ?>
                            <div>&bull; <?= e($err) ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px;">
                    🔒 Place Order & Pay
                </button>
            </div>

            <div class="card" style="padding: 16px; text-align: center; font-size: 12px; color: var(--text-muted);">
                🔒 Your payment information is secure
            </div>
        </div>
    </form>
</div>


<?php include APP_ROOT . "/app/Views/customer/customer_footer.php"; ?>