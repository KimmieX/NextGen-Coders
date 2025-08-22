<?php
class Product {
    // Database connection
    private static $pdo;

    // Product properties
    public $id;
    public $name;
    public $price;
    public $original_price;
    public $stock_quantity;
    public $category;
    public $image_url;
    public $description;
    public $featured;
    public $badge;
    public $expiry_date;
    public $created_at;
    public $updated_at;
    public $view_count;

    public static function setPDO($pdo) {
        self::$pdo = $pdo;
    }

    
    
   public function __construct($data = []) {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function save() {
    
    
    // Prepare data for insertion/update
    $data = [
        'name' => $this->name,
        'price' => $this->price,
        'original_price' => $this->original_price,
        'stock_quantity' => $this->stock_quantity,
        'category' => $this->category,
        'image_url' => $this->image_url,
        'description' => $this->description,
        'featured' => $this->featured,
        'badge' => $this->badge,
        'expiry_date' => $this->expiry_date
    ];
    
    try {
        if ($this->id) {
            // Update existing product
            $data['id'] = $this->id;
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $stmt = self::$pdo->prepare("UPDATE products SET 
                name = :name,
                price = :price,
                original_price = :original_price,
                stock_quantity = :stock_quantity,
                category = :category,
                image_url = :image_url,
                description = :description,
                featured = :featured,
                badge = :badge,
                expiry_date = :expiry_date,
                updated_at = :updated_at
                WHERE id = :id");
        } else {
            // Create new product
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $stmt = self::$pdo->prepare("INSERT INTO products (
                name, price, original_price, stock_quantity, category,
                image_url, description, featured, badge, expiry_date,
                created_at, updated_at
            ) VALUES (
                :name, :price, :original_price, :stock_quantity, :category,
                :image_url, :description, :featured, :badge, :expiry_date,
                :created_at, :updated_at
            )");
        }
        
        // Execute the query
        $stmt->execute($data);
        
        // Set the ID if this is a new product
        if (!$this->id) {
            $this->id = self::$pdo->lastInsertId();
        }
        
        return true;
        
    } catch (PDOException $e) {
        // Log the error (in production, use a proper logging system)
        error_log("Product save failed: " . $e->getMessage());
        
        // Return false or throw the exception depending on your needs
        return false;
        // Or: throw $e; // To let the calling code handle it
    }
}
    
   
   public static function getById($id) {
        $stmt = self::$pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        return $data ? new Product($data) : null;
    }
    
    public static function delete($id) {
        
        
        $stmt = self::$pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
   public static function getAll($limit = null, $offset = 0, $sort = 'newest') {
    $sortOptions = [
        'newest' => 'id DESC',
        'price_asc' => 'price ASC',
        'price_desc' => 'price DESC'
    ];
    $order = $sortOptions[$sort] ?? 'id DESC';
    
    $sql = "SELECT * FROM products ORDER BY $order";
    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$offset . "," . (int)$limit;
    }
    
    $stmt = self::$pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'Product');
}

  public static function getFeatured($limit = null, $offset = 0, $sortField = 'created_at', $sortOrder = 'DESC') {
    if (!self::$pdo) {
        throw new Exception("Database connection not initialized");
    }

    $sql = "SELECT * FROM products WHERE featured = 1";
    
    // Add sorting
    $validSortFields = ['created_at', 'price', 'view_count', 'name'];
    $sortField = in_array($sortField, $validSortFields) ? $sortField : 'created_at';
    $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
    $sql .= " ORDER BY $sortField $sortOrder";
    
    // Add limit/offset if provided
    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$offset . "," . (int)$limit;
    }
    
    try {
        $stmt = self::$pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Product');
    } catch (PDOException $e) {
        error_log("Featured products query failed: " . $e->getMessage());
        return [];
    }
}
    
    
    
   public static function getByCategory($category, $limit = null, $offset = 0, $sortField = 'created_at', $sortOrder = 'DESC') {
    if (!self::$pdo) {
        throw new Exception("Database connection not initialized");
    }

    $sql = "SELECT * FROM products WHERE category = ?";
    
    // Add sorting
    $validSortFields = ['created_at', 'price', 'view_count', 'name', '(original_price - price)'];
    $sortField = in_array($sortField, $validSortFields) ? $sortField : 'created_at';
    $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
    $sql .= " ORDER BY $sortField $sortOrder";
    
    // Add limit/offset if provided
    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$offset . "," . (int)$limit;
    }
    
    try {
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([$category]);
        $products = [];
        while ($row = $stmt->fetch()) {
            $products[] = new Product($row);
        }
        return $products;
    } catch (PDOException $e) {
        error_log("Category products query failed: " . $e->getMessage());
        return [];
    }
}

public function isOutOfStock() {
    return $this->stock_quantity <= 0;
}

    
   public function hasDiscount() {
    return $this->original_price > $this->price;
}

public function getDiscountPercentage() {
    if (!$this->hasDiscount()) return 0;
    return round((($this->original_price - $this->price) / $this->original_price) * 100);
}

    public static function getDiscountedProducts($limit = null, $offset = 0, $sortField = 'created_at', $sortOrder = 'DESC') {
    if (!self::$pdo) {
        throw new Exception("Database connection not initialized");
    }

    $sql = "SELECT * FROM products WHERE original_price > price";
    
    // Add sorting
    $validSortFields = ['created_at', 'price', 'view_count', 'name'];
    $sortField = in_array($sortField, $validSortFields) ? $sortField : 'created_at';
    $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
    $sql .= " ORDER BY $sortField $sortOrder";
    
    // Add limit/offset if provided
    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$offset . "," . (int)$limit;
    }
    
    try {
        $stmt = self::$pdo->query($sql);
        $products = [];
        while ($row = $stmt->fetch()) {
            $products[] = new Product($row);
        }
        return $products;
    } catch (PDOException $e) {
        error_log("Discounted products query failed: " . $e->getMessage());
        return [];
    }
}

public static function getByBadge($badge, $limit = null) {
    
    
    $sql = "SELECT * FROM products WHERE badge = ? ORDER BY id DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([$badge]);
    
    $products = [];
    while ($row = $stmt->fetch()) {
        $products[] = new Product($row);
    }
    return $products;
}

public static function getWishlistItems($userId) {

    
    $stmt = self::$pdo->prepare("SELECT p.* FROM products p 
                          JOIN wishlist w ON p.id = w.product_id 
                          WHERE w.user_id = ?");
    $stmt->execute([$userId]);
    
    $products = [];
    while ($row = $stmt->fetch()) {
        $products[] = new Product($row);
    }
    return $products;
}

public static function isInWishlist($userId, $productId) {
    
    
    $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM wishlist 
                          WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    
    return $stmt->fetchColumn() > 0;
}

public static function addToWishlist($userId, $productId) {
    
    
    if (self::isInWishlist($userId, $productId)) {
        return false;
    }
    
    $stmt = self::$pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    return $stmt->execute([$userId, $productId]);
}

public static function removeFromWishlist($userId, $productId) {
    
    
    $stmt = self::$pdo->prepare("DELETE FROM wishlist 
                          WHERE user_id = ? AND product_id = ?");
    return $stmt->execute([$userId, $productId]);
}

public static function getWishlistCount($userId) {
    
    $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}
public static function search($conditions = [], $params = [], $sortOrder = 'id DESC') {
        if (!self::$pdo) {
            throw new Exception("Database connection not initialized");
        }

        $where = empty($conditions) ? '' : ' WHERE ' . implode(' AND ', $conditions);
        $sql = "SELECT * FROM products" . $where . " ORDER BY " . $sortOrder;
        
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        
        $products = [];
        while ($row = $stmt->fetch()) {
            $products[] = new Product($row);
        }
        return $products;
    }

    public static function getCategories() {
        if (!self::$pdo) {
            throw new Exception("Database connection not initialized");
        }

        $stmt = self::$pdo->query("SELECT DISTINCT category FROM products");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

   

public static function getNewArrivals($limit = null, $offset = 0, $sortField = 'created_at', $sortOrder = 'DESC') {
    if (!self::$pdo) {
        throw new Exception("Database connection not initialized");
    }

    $sql = "SELECT * FROM products WHERE badge = 'New' ORDER BY $sortField $sortOrder";
    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$offset . "," . (int)$limit;
    }
    
    try {
        $stmt = self::$pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Product');
    } catch (PDOException $e) {
        error_log("Product query failed: " . $e->getMessage());
        return [];
    }
}

public static function count($conditions = []) {
    if (!self::$pdo) {
        throw new Exception("Database connection not initialized");
    }

    $where = '';
    $params = [];
    
    if (!empty($conditions)) {
        $whereParts = [];
        foreach ($conditions as $field => $value) {
            $whereParts[] = "$field = ?";
            $params[] = $value;
        }
        $where = ' WHERE ' . implode(' AND ', $whereParts);
    }

    try {
        $sql = "SELECT COUNT(*) FROM products" . $where;
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Count query failed: " . $e->getMessage());
        return 0;
    }
}
}
?>