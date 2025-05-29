<?php
$mysqli = new mysqli("localhost", "root", "php", "ecommerce_db");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Load and decode JSON file
$json = file_get_contents("data.json");
$data = json_decode($json, true);

// Check if products exist in the JSON structure
if (!isset($data['data']['products'])) {
    die("Product not found or invalid JSON.");
}

// Loop through each product
foreach ($data['data']['products'] as $product) {
    $name = $product['name'] ?? '';
    $description = $product['description'] ?? '';
    $category = $product['category'] ?? '';
    $brand = $product['brand'] ?? '';
    $in_stock = isset($product['inStock']) ? (int)$product['inStock'] : 0;
    $code_name = $product['id'] ?? ''; // use "id" from JSON as "code_name"

    // Insert product into the products table
    $stmt = $mysqli->prepare("INSERT INTO products (name, description, category, brand, in_stock, code_name) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $name, $description, $category, $brand, $in_stock, $code_name);     //"ssssis" indicates the expected data types: string, string, string, string, int, string.
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();

    // Insert product prices
    if (isset($product['prices']) && is_array($product['prices'])) {
        $stmt = $mysqli->prepare("INSERT INTO prices (product_id, amount, currency_label, currency_symbol) VALUES (?, ?, ?, ?)");
        foreach ($product['prices'] as $price) {
            $amount = $price['amount'] ?? 0;
            $currency_label = $price['currency']['label'] ?? '';
            $currency_symbol = $price['currency']['symbol'] ?? '';
            $stmt->bind_param("idss", $product_id, $amount, $currency_label, $currency_symbol);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Insert product images into the gallery table
    if (isset($product['gallery']) && is_array($product['gallery'])) {
        $stmt = $mysqli->prepare("INSERT INTO gallery (product_id, image_url) VALUES (?, ?)");
        foreach ($product['gallery'] as $image_url) {
            $stmt->bind_param("is", $product_id, $image_url);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Insert product attributes and their values
    if (isset($product['attributes']) && is_array($product['attributes'])) {
        foreach ($product['attributes'] as $attribute) {
            if (!isset($attribute['items']) || !is_array($attribute['items'])) {
                continue; // skip if attribute has no valid values. You can use this to skip nonexistant data.
            }

            $attr_name = $attribute['name'] ?? '';
            $attr_type = $attribute['type'] ?? '';
            $stmt = $mysqli->prepare("INSERT INTO attributes (product_id, attribute_name, type) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $product_id, $attr_name, $attr_type);
            $stmt->execute();
            $attribute_id = $stmt->insert_id;
            $stmt->close();

            // Insert attribute values (prepared once, executed multiple times)
            $stmt = $mysqli->prepare("INSERT INTO attribute_values (attribute_id, display_value, value) VALUES (?, ?, ?)");
            foreach ($attribute['items'] as $value) {
                $display_value = $value['displayValue'] ?? '';
                $real_value = $value['value'] ?? '';
                $stmt->bind_param("iss", $attribute_id, $display_value, $real_value);
                $stmt->execute();
            }
            $stmt->close();
        }
    }
}

echo "Import succesfull!";
?>
