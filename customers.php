<?php
// sessions for pages 
session_start();


$conn = mysqli_connect('localhost', 'root', '', 'agri_farming_db');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $first_name = sanitize($_POST['first_name']);
                $last_name = sanitize($_POST['last_name']);
                $email = sanitize($_POST['email']);
                $phone = sanitize($_POST['phone']);
                $address = sanitize($_POST['address']);
                $city = sanitize($_POST['city']);
                $state = sanitize($_POST['state']);
                $zip_code = sanitize($_POST['zip_code']);
                $country = sanitize($_POST['country']);
                $customer_type = sanitize($_POST['customer_type']);
                $business_name = sanitize($_POST['business_name']);
                $tax_id = sanitize($_POST['tax_id']);
                $status = sanitize($_POST['status']);
                $notes = sanitize($_POST['notes']);
                
                $checkEmailSQL = "SELECT id FROM customers WHERE email = '$email'";
                $result = mysqli_query($conn,$checkEmailSQL);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    $_SESSION['message'] = "Error: Email already exists!";
                    $_SESSION['msg_type'] = "danger";
                } else {
                    $sql = "INSERT INTO customers (first_name, last_name, email, phone, address, city, state, zip_code, country, customer_type, business_name, tax_id, status, notes) 
                           VALUES ('$first_name', '$last_name', '$email', '$phone', '$address', '$city', '$state', '$zip_code', '$country', '$customer_type', '$business_name', '$tax_id', '$status', '$notes')";
                    
                    if (mysqli_query($conn,$sql)) {
                        $_SESSION['message'] = "Customer added successfully!";
                        $_SESSION['msg_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Error adding customer: " . $conn->error;
                        $_SESSION['msg_type'] = "danger";
                    }
                }
                break;
                
            case 'update':
                $id = intval($_POST['id']);
                $first_name = sanitize($_POST['first_name']);
                $last_name = sanitize($_POST['last_name']);
                $email = sanitize($_POST['email']);
                $phone = sanitize($_POST['phone']);
                $address = sanitize($_POST['address']);
                $city = sanitize($_POST['city']);
                $state = sanitize($_POST['state']);
                $zip_code = sanitize($_POST['zip_code']);
                $country = sanitize($_POST['country']);
                $customer_type = sanitize($_POST['customer_type']);
                $business_name = sanitize($_POST['business_name']);
                $tax_id = sanitize($_POST['tax_id']);
                $status = sanitize($_POST['status']);
                $notes = sanitize($_POST['notes']);
                
                // Check if email already exists for another customer
                $checkEmailSQL = "SELECT id FROM customers WHERE email = '$email' AND id != $id";
                $result = mysqli_query($conn,$checkEmailSQL);
                
                if ($result && $result->num_rows > 0) {
                    $_SESSION['message'] = "Error: Email already exists for another customer!";
                    $_SESSION['msg_type'] = "danger";
                } else {
                    $sql = "UPDATE customers SET 
                            first_name = '$first_name',
                            last_name = '$last_name',
                            email = '$email',
                            phone = '$phone',
                            address = '$address',
                            city = '$city',
                            state = '$state',
                            zip_code = '$zip_code',
                            country = '$country',
                            customer_type = '$customer_type',
                            business_name = '$business_name',
                            tax_id = '$tax_id',
                            status = '$status',
                            notes = '$notes'
                            WHERE id = $id";
                    
                    if (mysqli_query($conn,$sql)) {
                        $_SESSION['message'] = "Customer updated successfully!";
                        $_SESSION['msg_type'] = "success";
                    } else {
                        $_SESSION['message'] = "Error updating customer: " . $conn->error;
                        $_SESSION['msg_type'] = "danger";
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                $sql = "DELETE FROM customers WHERE id = $id";
                
                if ( mysqli_query($conn,$sql)) {
                    $_SESSION['message'] = "Customer deleted successfully!";
                    $_SESSION['msg_type'] = "success";
                } else {
                    $_SESSION['message'] = "Error deleting customer: " . $conn->error;
                    $_SESSION['msg_type'] = "danger";
                }
                break;
        }
        dshfdshfg
        // Redirect to prevent form resubmission
        header(header: "Location: customers.php");
        exit();
    }
}

//  included header file 
include 'header.php';

// Fetch all customers with search functionality
$search = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = sanitize($_GET['search']);
    $sql = "SELECT * FROM customers 
            WHERE first_name LIKE '%$search%' 
            OR last_name LIKE '%$search%' 
            OR email LIKE '%$search%' 
            OR phone LIKE '%$search%'
            OR business_name LIKE '%$search%'
            ORDER BY created_at DESC";
} else {
    $sql = "SELECT * FROM customers ORDER BY created_at DESC";
}

$result = mysqli_query($conn,$sql);
$customers = [];
if ($result) {
    $customers = $result->fetch_all(MYSQLI_ASSOC);
}

// Get customer count by type for statistics
$statsResult = mysqli_query($conn,"
    SELECT customer_type, COUNT(*) as count, 
           SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active_count
    FROM customers 
    GROUP BY customer_type
");
$customerStats = [];
if ($statsResult) {
    $customerStats = $statsResult->fetch_all(MYSQLI_ASSOC);
}

// Get total customer count
$totalResult = mysqli_query($conn,"SELECT COUNT(*) as total FROM customers");
$totalCustomers = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Customers Management</h1>
                </div>
                <div class="col-sm-6">
                    <button type="button" class="btn btn-success float-right" data-toggle="modal" data-target="#addCustomerModal">
                        <i class="fas fa-user-plus"></i> Add New Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?php echo $totalCustomers; ?></h3>
                            <p>Total Customers</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <?php 
                $typeColors = ['Individual' => 'success', 'Business' => 'warning', 'Farm' => 'primary', 'Dealer' => 'danger'];
                foreach ($customerStats as $stat): 
                    $color = $typeColors[$stat['customer_type']] ?? 'secondary';
                ?>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-<?php echo $color; ?>">
                        <div class="inner">
                            <h3><?php echo $stat['count']; ?></h3>
                            <p><?php echo $stat['customer_type']; ?> Customers</p>
                            <small><?php echo $stat['active_count']; ?> Active</small>
                        </div>
                        <div class="icon">
                            <i class="fas fa-<?php echo $stat['customer_type'] == 'Farm' ? 'tractor' : ($stat['customer_type'] == 'Business' ? 'building' : ($stat['customer_type'] == 'Dealer' ? 'store' : 'user')); ?>"></i>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Success/Error Messages -->
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['message']; 
                        unset($_SESSION['message']);
                        unset($_SESSION['msg_type']);
                    ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Customers Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Customers</h3>
                    <div class="card-tools">
                        <form method="GET" class="form-inline">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control float-right" 
                                       placeholder="Search customers..." value="<?php echo htmlspecialchars($search); ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <?php if(!empty($search)): ?>
                                    <a href="customers.php" class="btn btn-default">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Business</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($customers) > 0): ?>
                                <?php foreach($customers as $customer): ?>
                                    <?php 
                                    $statusColors = ['Active' => 'success', 'Inactive' => 'danger', 'Pending' => 'warning'];
                                    $typeColors = ['Individual' => 'info', 'Business' => 'warning', 'Farm' => 'success', 'Dealer' => 'primary'];
                                    ?>
                                    <tr>
                                        <td><?php echo $customer['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $typeColors[$customer['customer_type']] ?? 'secondary'; ?>">
                                                <?php echo $customer['customer_type']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if(!empty($customer['business_name'])): ?>
                                                <span class="text-muted"><?php echo htmlspecialchars($customer['business_name']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($customer['city']) && !empty($customer['state'])): ?>
                                                <?php echo htmlspecialchars($customer['city'] . ', ' . $customer['state']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $statusColors[$customer['status']] ?? 'secondary'; ?>">
                                                <?php echo $customer['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" 
                                                    data-target="#viewCustomerModal<?php echo $customer['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" 
                                                    data-target="#editCustomerModal<?php echo $customer['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" 
                                                    data-target="#deleteCustomerModal<?php echo $customer['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- View Customer Modal -->
                                    <div class="modal fade" id="viewCustomerModal<?php echo $customer['id']; ?>" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Customer Details</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h4><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></h4>
                                                            <div class="row mt-3">
                                                                <div class="col-md-6">
                                                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                                                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer['phone']); ?></p>
                                                                    <p><strong>Customer Type:</strong> 
                                                                        <span class="badge badge-<?php echo $typeColors[$customer['customer_type']] ?? 'secondary'; ?>">
                                                                            <?php echo $customer['customer_type']; ?>
                                                                        </span>
                                                                    </p>
                                                                    <p><strong>Status:</strong> 
                                                                        <span class="badge badge-<?php echo $statusColors[$customer['status']] ?? 'secondary'; ?>">
                                                                            <?php echo $customer['status']; ?>
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <?php if(!empty($customer['business_name'])): ?>
                                                                        <p><strong>Business Name:</strong> <?php echo htmlspecialchars($customer['business_name']); ?></p>
                                                                    <?php endif; ?>
                                                                    <?php if(!empty($customer['tax_id'])): ?>
                                                                        <p><strong>Tax ID:</strong> <?php echo htmlspecialchars($customer['tax_id']); ?></p>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            
                                                            <h5 class="mt-4">Address Information</h5>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['address']); ?></p>
                                                                    <p><strong>City:</strong> <?php echo htmlspecialchars($customer['city']); ?></p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p><strong>State:</strong> <?php echo htmlspecialchars($customer['state']); ?></p>
                                                                    <p><strong>Zip Code:</strong> <?php echo htmlspecialchars($customer['zip_code']); ?></p>
                                                                    <p><strong>Country:</strong> <?php echo htmlspecialchars($customer['country']); ?></p>
                                                                </div>
                                                            </div>
                                                            
                                                            <?php if(!empty($customer['notes'])): ?>
                                                                <h5 class="mt-4">Notes</h5>
                                                                <p><?php echo nl2br(htmlspecialchars($customer['notes'])); ?></p>
                                                            <?php endif; ?>
                                                            
                                                            <div class="mt-4 text-muted">
                                                                <small><strong>Created:</strong> <?php echo $customer['created_at']; ?></small><br>
                                                                <small><strong>Last Updated:</strong> <?php echo $customer['updated_at']; ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Customer Modal -->
                                    <div class="modal fade" id="editCustomerModal<?php echo $customer['id']; ?>" tabindex="-1" role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Customer</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="first_name<?php echo $customer['id']; ?>">First Name *</label>
                                                                    <input type="text" class="form-control" id="first_name<?php echo $customer['id']; ?>" 
                                                                           name="first_name" value="<?php echo htmlspecialchars($customer['first_name']); ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="last_name<?php echo $customer['id']; ?>">Last Name *</label>
                                                                    <input type="text" class="form-control" id="last_name<?php echo $customer['id']; ?>" 
                                                                           name="last_name" value="<?php echo htmlspecialchars($customer['last_name']); ?>" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="email<?php echo $customer['id']; ?>">Email *</label>
                                                                    <input type="email" class="form-control" id="email<?php echo $customer['id']; ?>" 
                                                                           name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="phone<?php echo $customer['id']; ?>">Phone</label>
                                                                    <input type="text" class="form-control" id="phone<?php echo $customer['id']; ?>" 
                                                                           name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <label for="address<?php echo $customer['id']; ?>">Address</label>
                                                            <input type="text" class="form-control" id="address<?php echo $customer['id']; ?>" 
                                                                   name="address" value="<?php echo htmlspecialchars($customer['address']); ?>">
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="city<?php echo $customer['id']; ?>">City</label>
                                                                    <input type="text" class="form-control" id="city<?php echo $customer['id']; ?>" 
                                                                           name="city" value="<?php echo htmlspecialchars($customer['city']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="state<?php echo $customer['id']; ?>">State</label>
                                                                    <input type="text" class="form-control" id="state<?php echo $customer['id']; ?>" 
                                                                           name="state" value="<?php echo htmlspecialchars($customer['state']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="zip_code<?php echo $customer['id']; ?>">Zip Code</label>
                                                                    <input type="text" class="form-control" id="zip_code<?php echo $customer['id']; ?>" 
                                                                           name="zip_code" value="<?php echo htmlspecialchars($customer['zip_code']); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="country<?php echo $customer['id']; ?>">Country</label>
                                                                    <input type="text" class="form-control" id="country<?php echo $customer['id']; ?>" 
                                                                           name="country" value="<?php echo htmlspecialchars($customer['country']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="customer_type<?php echo $customer['id']; ?>">Customer Type</label>
                                                                    <select class="form-control" id="customer_type<?php echo $customer['id']; ?>" name="customer_type">
                                                                        <option value="Individual" <?php echo $customer['customer_type'] == 'Individual' ? 'selected' : ''; ?>>Individual</option>
                                                                        <option value="Business" <?php echo $customer['customer_type'] == 'Business' ? 'selected' : ''; ?>>Business</option>
                                                                        <option value="Farm" <?php echo $customer['customer_type'] == 'Farm' ? 'selected' : ''; ?>>Farm</option>
                                                                        <option value="Dealer" <?php echo $customer['customer_type'] == 'Dealer' ? 'selected' : ''; ?>>Dealer</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="business_name<?php echo $customer['id']; ?>">Business Name</label>
                                                                    <input type="text" class="form-control" id="business_name<?php echo $customer['id']; ?>" 
                                                                           name="business_name" value="<?php echo htmlspecialchars($customer['business_name']); ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="tax_id<?php echo $customer['id']; ?>">Tax ID</label>
                                                                    <input type="text" class="form-control" id="tax_id<?php echo $customer['id']; ?>" 
                                                                           name="tax_id" value="<?php echo htmlspecialchars($customer['tax_id']); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="status<?php echo $customer['id']; ?>">Status</label>
                                                                    <select class="form-control" id="status<?php echo $customer['id']; ?>" name="status">
                                                                        <option value="Active" <?php echo $customer['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                                                                        <option value="Inactive" <?php echo $customer['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                                        <option value="Pending" <?php echo $customer['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <label for="notes<?php echo $customer['id']; ?>">Notes</label>
                                                            <textarea class="form-control" id="notes<?php echo $customer['id']; ?>" name="notes" rows="3"><?php echo htmlspecialchars($customer['notes']); ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update Customer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Customer Modal -->
                                    <div class="modal fade" id="deleteCustomerModal<?php echo $customer['id']; ?>" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form method="POST">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                                                        <p>Are you sure you want to delete the customer "<strong><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></strong>"?</p>
                                                        <p class="text-danger">This action cannot be undone and will delete all associated data.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete Customer</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">No customers found. Add your first customer!</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
                    <div class="float-right">
                        Showing <?php echo count($customers); ?> customers
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_first_name">First Name *</label>
                                <input type="text" class="form-control" id="new_first_name" name="first_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_last_name">Last Name *</label>
                                <input type="text" class="form-control" id="new_last_name" name="last_name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_email">Email *</label>
                                <input type="email" class="form-control" id="new_email" name="email" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_phone">Phone</label>
                                <input type="text" class="form-control" id="new_phone" name="phone">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_address">Address</label>
                        <input type="text" class="form-control" id="new_address" name="address">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="new_city">City</label>
                                <input type="text" class="form-control" id="new_city" name="city">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="new_state">State</label>
                                <input type="text" class="form-control" id="new_state" name="state">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="new_zip_code">Zip Code</label>
                                <input type="text" class="form-control" id="new_zip_code" name="zip_code">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_country">Country</label>
                                <input type="text" class="form-control" id="new_country" name="country" value="USA">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_customer_type">Customer Type</label>
                                <select class="form-control" id="new_customer_type" name="customer_type">
                                    <option value="Individual">Individual</option>
                                    <option value="Business">Business</option>
                                    <option value="Farm">Farm</option>
                                    <option value="Dealer">Dealer</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_business_name">Business Name</label>
                                <input type="text" class="form-control" id="new_business_name" name="business_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_tax_id">Tax ID</label>
                                <input type="text" class="form-control" id="new_tax_id" name="tax_id">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_status">Status</label>
                                <select class="form-control" id="new_status" name="status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Pending">Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_notes">Notes</label>
                        <textarea class="form-control" id="new_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// Close database connection
$conn->close();
include 'footer.php'; 
?>