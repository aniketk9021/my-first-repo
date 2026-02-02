<?php
$host     = "localhost";
$username = "root";
$password = "";
$database = "mydb";

/* -----------------------------
   DATABASE CONNECTION
------------------------------*/
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

/* -----------------------------
   CREATE CATEGORY TABLE IF NOT EXISTS
------------------------------*/
$createCategoryTableSql = "
CREATE TABLE IF NOT EXISTS tbl_category (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    category_description VARCHAR(255),
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $createCategoryTableSql)) {
    // Don't echo here to avoid header errors
    error_log("Table creation error: " . mysqli_error($conn));
}
?>