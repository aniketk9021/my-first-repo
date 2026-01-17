<?php
include 'header.php';
require_once __DIR__ . '/db/db.php';

/* ------------------------
   INSERT CATEGORY
-------------------------*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
            header("Location: category.php");
            exit;
        } else {
            echo "Insert Error: " . mysqli_error($conn);
        }
    } else {
        echo "Category name is required";
    }
}
?>

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

    <!-- ADD CATEGORY MODAL -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Category</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category Name *</label>
                                    <input type="text" class="form-control" name="category_name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category Description</label>
                                    <input type="text" class="form-control" name="category_description">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Category</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- CATEGORY TABLE -->
    <table border="1" style="margin-left:30px;width:800px;text-align:center;">
        <tr>
            <th>Category Name</th>
            <th>Category Description</th>
        </tr>

        <?php
        $selectQuery = "SELECT * FROM tbl_category";
        $result = mysqli_query($conn, $selectQuery);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['category_name']}</td>";
            echo "<td>{$row['category_description']}</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<?php include 'footer.php'; ?>
