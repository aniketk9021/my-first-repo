<?php
include 'header.php';
include './db/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "hello";
    echo $catgoryName = $_POST['category_name'];
    echo $catgoryDescription = $_POST['category_description'];

    $insertQuery = "INSERT INTO tbl_category(category_name,category_description)VALUES('$catgoryName','$catgoryDescription');";

    $result = mysqli_query($conn, $insertQuery);
    if ($result) {
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
                        data-target="#addCustomerModal">
                        <i class="fas fa-user-plus"></i> Add New Category
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_first_name">Category Name *</label>
                                    <input type="text" class="form-control" id="new_first_name" name="category_name"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_last_name">Category Description</label>
                                    <input type="text" class="form-control" id="new_last_name"
                                        name="category_description">
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
    <table border="1" style=" margin-left : 30px; width: 800px; text-align: center;">
        <tr>
            <th>Category Name</th>
            <th>Category Description</th>
        </tr>
        <?php
        $selectQuery = "SELECT * FROM tbl_category";
        $result = mysqli_query($conn, $selectQuery);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['category_name'] . "</td>";
            echo "<td>" . $row['category_description'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

</div>
<?php include 'footer.php' ?>