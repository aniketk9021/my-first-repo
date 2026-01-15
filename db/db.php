<?php
$host     = 'localhost';
$username = 'root';
$password = '';
$database = 'mydb';

// CONNECT WITH DATABASE
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// CREATE CATEGORY TABLE
$createCategoryTableSql = "
CREATE TABLE IF NOT EXISTS `tbl_category` (
    `category_id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_name` VARCHAR(100) NOT NULL,
    `category_description` VARCHAR(100) NOT NULL,
    `status` INT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $createCategoryTableSql)) {
    // echo "✅ Category table created successfully";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}
?>
