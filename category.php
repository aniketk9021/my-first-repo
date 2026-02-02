<?php
// START - Output buffering to prevent header errors
ob_start();

// Start session at the VERY beginning
session_start();

include 'header.php';
require_once __DIR__ . '/db/db.php';

/* ------------------------
   INSERT CATEGORY (CREATE)
-------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $categoryName = trim($_POST['category_name']);
    $categoryDescription = trim($_POST['category_description']);

    if (!empty($categoryName)) {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO tbl_category (category_name, category_description) VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "ss", $categoryName, $categoryDescription);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $_SESSION['message'] = "Category added successfully!";
            $_SESSION['message_type'] = "success";
            ob_end_clean(); // Clear buffer before redirect
            header("Location: category.php");
            exit();
        } else {
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Category name is required";
        $_SESSION['message_type'] = "error";
    }
}

/* ------------------------
   UPDATE CATEGORY
-------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
    $categoryId = $_POST['category_id'];
    $categoryName = trim($_POST['category_name']);
    $categoryDescription = trim($_POST['category_description']);

    if (!empty($categoryName)) {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE tbl_category SET category_name = ?, category_description = ? WHERE category_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "ssi", $categoryName, $categoryDescription, $categoryId);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $_SESSION['message'] = "Category updated successfully!";
            $_SESSION['message_type'] = "success";
            ob_end_clean(); // Clear buffer before redirect
            header("Location: category.php");
            exit();
        } else {
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Category name is required";
        $_SESSION['message_type'] = "error";
    }
}

/* ------------------------
   DELETE CATEGORY
-------------------------*/
if (isset($_GET['delete_id'])) {
    $categoryId = intval($_GET['delete_id']);
    
    // First, check if category exists
    $checkStmt = mysqli_prepare($conn, "SELECT * FROM tbl_category WHERE category_id = ?");
    mysqli_stmt_bind_param($checkStmt, "i", $categoryId);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $row = mysqli_fetch_assoc($checkResult);
        $categoryName = $row['category_name'];
        
        $stmt = mysqli_prepare($conn, "DELETE FROM tbl_category WHERE category_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $_SESSION['message'] = "Category '{$categoryName}' deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error deleting category: " . mysqli_error($conn);
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Category not found!";
        $_SESSION['message_type'] = "error";
    }
    ob_end_clean(); // Clear buffer before redirect
    header("Location: category.php");
    exit();
}

// END output buffering - flush the buffer
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .action-buttons {
            white-space: nowrap;
        }
        .btn-action {
            margin: 2px;
        }
        table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Category Management</h1>
                </div>
                <div class="col-sm-6">
                    <button type="button" class="btn btn-success float-right" data-toggle="modal"
                        data-target="#addCategoryModal">
                        <i class="fas fa-plus"></i> Add New Category
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="container-fluid mt-3">
            <div class="alert alert-<?php echo ($_SESSION['message_type'] == 'error') ? 'danger' : 'success'; ?> alert-dismissible fade show">
                <?php 
                echo htmlspecialchars($_SESSION['message']);
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <!-- ADD CATEGORY MODAL -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Category</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category Name *</label>
                                    <input type="text" class="form-control" name="category_name" required 
                                           placeholder="Enter category name">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category Description</label>
                                    <input type="text" class="form-control" name="category_description" 
                                           placeholder="Enter description (optional)">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_category" class="btn btn-success">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT CATEGORY MODAL -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Category</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category Name *</label>
                                    <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                                    <input type="hidden" id="edit_category_id" name="category_id">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category Description</label>
                                    <input type="text" class="form-control" id="edit_category_description" name="category_description">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_category" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CATEGORY TABLE -->
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Category List</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10%">ID</th>
                                <th width="30%">Category Name</th>
                                <th width="40%">Category Description</th>
                                <th width="20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $selectQuery = "SELECT * FROM tbl_category ORDER BY category_id DESC";
                            $result = mysqli_query($conn, $selectQuery);

                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>{$row['category_id']}</td>";
                                    echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['category_description'] ?? '') . "</td>";
                                    echo "<td class='action-buttons'>";
                                    echo "<button class='btn btn-sm btn-primary btn-action edit-btn' 
                                            data-id='{$row['category_id']}'
                                            data-name='" . htmlspecialchars($row['category_name']) . "'
                                            data-description='" . htmlspecialchars($row['category_description'] ?? '') . "'>
                                            <i class='fas fa-edit'></i> Edit
                                          </button>";
                                    echo "<button class='btn btn-sm btn-danger btn-action delete-btn' 
                                            data-id='{$row['category_id']}'
                                            data-name='" . htmlspecialchars($row['category_name']) . "'>
                                            <i class='fas fa-trash'></i> Delete
                                          </button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center text-muted'>No categories found. Add your first category!</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Edit button click handler
    $('.edit-btn').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const categoryId = $(this).data('id');
        const categoryName = $(this).data('name');
        const categoryDescription = $(this).data('description');
        
        // Set values in edit modal
        $('#edit_category_id').val(categoryId);
        $('#edit_category_name').val(categoryName);
        $('#edit_category_description').val(categoryDescription);
        
        // Show modal
        $('#editCategoryModal').modal('show');
    });
    
    // Delete button click handler
    $('.delete-btn').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const categoryId = $(this).data('id');
        const categoryName = $(this).data('name');
        
        if (confirm('Are you sure you want to delete category: "' + categoryName + '"?')) {
            window.location.href = 'category.php?delete_id=' + categoryId;
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
});
</script>

<?php include 'footer.php'; ?>
</body>
</html>