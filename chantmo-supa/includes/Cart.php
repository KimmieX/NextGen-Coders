<?php
require_once __DIR__ . '/Product.php';

class Cart {
    public static function initCart() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public static function addItem($productId, $quantity = 1) {
        self::initCart();
        
        $product = Product::getById($productId);
        if (!$product || $product->stock_quantity <= 0) {
            return false;
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

        // Ensure we don't exceed available stock
        if ($_SESSION['cart'][$productId] > $product->stock_quantity) {
            $_SESSION['cart'][$productId] = $product->stock_quantity;
        }

        return true;
    }

    public static function removeItem($productId) {
        self::initCart();
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            return true;
        }
        return false;
    }

    public static function updateQuantity($productId, $quantity) {
        self::initCart();
        
        $product = Product::getById($productId);
        if (!$product || $quantity <= 0) {
            self::removeItem($productId);
            return false;
        }

        // Don't allow adding more than available stock
        $quantity = min($quantity, $product->stock_quantity);
        $_SESSION['cart'][$productId] = $quantity;
        return true;
    }

    public static function getCartItems() {
        self::initCart();
        return $_SESSION['cart'] ?? [];
    }

    public static function getTotalItems() {
        return array_sum(self::getCartItems());
    }

    public static function getTotalPrice() {
        $total = 0;
        foreach (self::getCartItems() as $productId => $quantity) {
            $product = Product::getById($productId);
            if ($product) {
                $total += $product->price * $quantity;
            }
        }
        return $total;
    }

    public static function clear() {
        self::initCart();
        $_SESSION['cart'] = [];
    }

    public static function getCartDetails() {
        $items = [];
        $total = 0;
        
        foreach (self::getCartItems() as $productId => $quantity) {
            $product = Product::getById($productId);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                $total += $product->price * $quantity;
            }
        }
        
        return [
            'items' => $items,
            'total' => $total,
            'count' => self::getTotalItems()
        ];
    }
}