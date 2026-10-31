
<?php
require_once __DIR__ . "/../../Models/Product.php";
class CartController
{
    private const CART_LIFETIME = 86400; // 24 hours

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {

            ini_set(
                "session.gc_maxlifetime",
                (string) self::CART_LIFETIME
            );

            session_set_cookie_params([
                "lifetime" => self::CART_LIFETIME,
                "path" => "/",
                "httponly" => true,
                "samesite" => "Lax",
                "secure" => !empty($_SERVER["HTTPS"])
                    && $_SERVER["HTTPS"] !== "off"
            ]);

            session_start();
        }

        // Create cart if it doesn't exist
        if (!isset($_SESSION["cart"])) {
            $_SESSION["cart"] = [];
        }

        // Set expiration time
        if (!isset($_SESSION["cart_expires_at"])) {
            $_SESSION["cart_expires_at"] =
                time() + self::CART_LIFETIME;
        }

        // Clear cart after 24 hours
        if (time() >= $_SESSION["cart_expires_at"]) {
            $_SESSION["cart"] = [];

            $_SESSION["cart_expires_at"] =
                time() + self::CART_LIFETIME;
        }
    }

    public function addToCart(
        PDO $pdo,
        int $productId,
        int $quantity = 1,
        string $size = ""
    ): array {

        if ($productId <= 0 || $quantity <= 0) {
            throw new InvalidArgumentException(
                "Invalid product or quantity"
            );
        }

        $productModel = new ProductModel($pdo);

        $product = $productModel->getProductById($productId);

        // 1. Check if product exists
        if (!$product) {
            throw new InvalidArgumentException(
                "Product not available"
            );
        }

        // 2. Convert sizes JSON to PHP array
        $availableSizes = json_decode(
            $product["sizes"] ?? "[]",
            true
        );

        // 3. Validate sizes data
        if (!is_array($availableSizes)) {
            throw new InvalidArgumentException(
                "Invalid product sizes"
            );
        }

        // 4. Check selected size
        if (!in_array($size, $availableSizes, true)) {
            throw new InvalidArgumentException(
                "Selected size is not available"
            );
        }


        if (!$product) {
            throw new InvalidArgumentException(
                "Product not available"
            );
        }

        // Check whether the product is active
        $stmt = $pdo->prepare(
            "SELECT is_active FROM products WHERE id = :id"
        );

        $stmt->execute([
            "id" => $productId
        ]);

        $isActive = $stmt->fetchColumn();

        if ((int)$isActive !== 1) {
            throw new InvalidArgumentException(
                "Product is inactive"
            );
        }


        $key = $productId . ":" . $size;

        $currentQuantity =
            $_SESSION["cart"][$key]["quantity"] ?? 0;

        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity > (int)$product["stock"]) {
            throw new InvalidArgumentException(
                "Not enough stock"
            );
        }

        $_SESSION["cart"][$key] = [
            "product_id" => $productId,
            "size" => $size,
            "quantity" => $newQuantity
        ];

        return $_SESSION["cart"];
    }


    public function getCart(): array
    {
        return $_SESSION["cart"] ?? [];
    }


    public function getCartDetails(PDO $pdo): array
    {
        $items = [];
        $total = 0;

        foreach ($this->getCart() as $cartItem) {

            $stmt = $pdo->prepare(
                "SELECT id, name, price, discount_percent, stock, is_active
     FROM products
     WHERE id = :id"
            );

            $stmt->execute([
                "id" => $cartItem["product_id"]
            ]);

            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || (int)$product["is_active"] !== 1) {
                continue;
            }


            $quantity = (int) $cartItem["quantity"];

            // Original price
            $price = (float) $product["price"];

            // Discount percentage
            $discountPercent = (float) ($product["discount_percent"] ?? 0);

            // Calculate discount amount
            $discountAmount = $price * ($discountPercent / 100);

            // Price after discount
            $finalPrice = round($price - $discountAmount, 2);

            // Calculate item subtotal
            $subtotal = $finalPrice * $quantity;

            // Add to cart total
            $total += $subtotal;


            $items[] = [
                "product_id" => (int)$product["id"],
                "name" => $product["name"],
                "size" => $cartItem["size"],
                "quantity" => $quantity,
                "original_price" => number_format($price, 2, ".", ""),
                "discount_percent" => $discountPercent,
                "price" => number_format($finalPrice, 2, ".", ""),
                "subtotal" => number_format($subtotal, 2, ".", "")
            ];
        }

        return [
            "items" => $items,
            "total" => number_format($total, 2, ".", ""),
            "currency" => "JOD"
        ];
    }


    public function validateCart(PDO $pdo): array
    {
        $cart = $this->getCart();

        if (empty($cart)) {
            throw new InvalidArgumentException("Cart is empty");
        }

        $errors = [];
        $quantitiesByProduct = [];

        foreach ($cart as $item) {

            $productId = (int) $item["product_id"];
            $quantity = (int) $item["quantity"];
            $size = $item["size"];

            $stmt = $pdo->prepare(
                "SELECT id, stock, sizes, is_active
             FROM products
             WHERE id = :id"
            );

            $stmt->execute(["id" => $productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product || (int)$product["is_active"] !== 1) {
                $errors[] = "Product $productId is unavailable";
                continue;
            }

            $availableSizes = json_decode(
                $product["sizes"] ?? "[]",
                true
            );

            if (
                !is_array($availableSizes) ||
                !in_array($size, $availableSizes, true)
            ) {
                $errors[] = "Invalid size for product $productId";
            }

            if ($quantity <= 0) {
                $errors[] = "Invalid quantity for product $productId";
                continue;
            }

            // Combine quantities across sizes
            $quantitiesByProduct[$productId] =
                ($quantitiesByProduct[$productId] ?? 0) + $quantity;

            if ($quantitiesByProduct[$productId] > (int)$product["stock"]) {
                $errors[] = "Not enough stock for product $productId";
            }
        }

        return [
            "valid" => empty($errors),
            "errors" => $errors
        ];
    }


    public function updateCartQuantity(
        PDO $pdo,
        int $productId,
        int $quantity,
        string $size = ""
    ): array {

        // 1. Validate input
        if ($productId <= 0 || $quantity <= 0) {
            throw new InvalidArgumentException(
                "Invalid product or quantity"
            );
        }

        // 2. Find the product in session cart
        $key = $productId . ":" . $size;

        if (!isset($_SESSION["cart"][$key])) {
            throw new InvalidArgumentException(
                "Product not found in cart"
            );
        }

        // 3. Check available stock
        $stmt = $pdo->prepare(
            "SELECT stock, is_active
         FROM products
         WHERE id = :id"
        );

        $stmt->execute([
            "id" => $productId
        ]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product || (int)$product["is_active"] !== 1) {
            throw new InvalidArgumentException(
                "Product not available"
            );
        }

        if ($quantity > (int)$product["stock"]) {
            throw new InvalidArgumentException(
                "Not enough stock"
            );
        }

        // 4. Update quantity in session
        $_SESSION["cart"][$key]["quantity"] = $quantity;

        // 5. Return updated cart
        return $_SESSION["cart"];
    }

    public function removeFromCart(
        int $productId,
        string $size = ""
    ): array {

        // 1. Validate product ID
        if ($productId <= 0) {
            throw new InvalidArgumentException(
                "Invalid product ID"
            );
        }

        // 2. Create cart item key
        $key = $productId . ":" . $size;

        // 3. Check if item exists
        if (!isset($_SESSION["cart"][$key])) {
            throw new InvalidArgumentException(
                "Product not found in cart"
            );
        }

        // 4. Remove item from session
        unset($_SESSION["cart"][$key]);

        // 5. Return updated cart
        return $_SESSION["cart"];
    }

public function clearCart(): array
{
    // Remove all products from cart
    $_SESSION["cart"] = [];

    // Return empty cart
    return $_SESSION["cart"];
}

public function getCartCount(): int
{
    $count = 0;

    foreach ($this->getCart() as $item) {
        $count += (int) $item["quantity"];
    }

    return $count;
}


}
