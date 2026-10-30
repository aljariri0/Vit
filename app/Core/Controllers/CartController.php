
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
            "SELECT id, name, price, stock, is_active
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

        $quantity = (int)$cartItem["quantity"];
        $price = (float)$product["price"];

        $subtotal = $price * $quantity;
        $total += $subtotal;

        $items[] = [
            "product_id" => (int)$product["id"],
            "name" => $product["name"],
            "size" => $cartItem["size"],
            "quantity" => $quantity,
            "price" => number_format($price, 2, ".", ""),
            "subtotal" => number_format($subtotal, 2, ".", "")
        ];
    }

    return [
        "items" => $items,
        "total" => number_format($total, 2, ".", ""),
        "currency" => "JOD"
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




}
