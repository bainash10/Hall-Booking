<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Custom Styles -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="logout.php">
          Logout <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
      <span class="brand-text font-weight-light">Book The Hall</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <?php if (in_array($user['role'], ['HOD', 'EXAMSECTION'])): ?>
          <li class="nav-item">
            <a href="book_hall.php" class="nav-link">
              <i class="nav-icon fas fa-book"></i>
              <p>
                Book Hall
              </p>
            </a>
          </li>
          <?php endif; ?>
          <?php if (in_array($user['role'], ['ADMINISTRATIVE', 'PRINCIPAL'])): ?>
          <li class="nav-item">
            <a href="approve_request.php" class="nav-link">
              <i class="nav-icon fas fa-check"></i>
              <p>
                View Requests
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="registered_users.php" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Registered Users
              </p>
            </a>
          </li>
          <?php endif; ?>
          <?php if ($user['role'] == 'ADMINISTRATIVE'): ?>
          <li class="nav-item">
            <a href="register.php" class="nav-link">
              <i class="nav-icon fas fa-user-plus"></i>
              <p>
                Register User
              </p>
            </a>
          </li>
          <?php endif; ?>
          <li class="nav-item">
            <a href="view_profile.php" class="nav-link">
              <i class="nav-icon fas fa-user"></i>
              <p>
                View Profile
              </p>
            </a>
          </li>
          <?php if (in_array($user['role'], ['HOD', 'EXAMSECTION'])): ?>
          <li class="nav-item">
            <a href="view_bookings.php" class="nav-link">
              <i class="nav-icon fas fa-calendar"></i>
              <p>
                My Bookings
              </p>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Dashboard</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <!-- Breadcrumb -->
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Main row -->
      <div class="row">
        <!-- Welcome Message -->
        <div class="col-md-12">
          <div class="alert alert-info">
            <h5>Welcome, <?php echo $user['name']; ?></h5>
            <p>Your role: <?php echo $user['role']; ?></p>
          </div>
        </div>

        <!-- Sidebar Menu Cards -->
        <div class="col-md-4">
              <!-- Dashboard Menu Card -->
                  <!-- Dashboard Menu Card -->
<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title">Dashboard Menu</h3>
    </div>
    <div class="card-body">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            <div class="col mb-4">
                <div class="bg-info p-4 rounded text-center h-100 d-flex align-items-center justify-content-center">
                    <h5><a href="dashboard.php" class="text-white text-decoration-none">Dashboard</a></h5>
                </div>
            </div>
            <?php if (in_array($user['role'], ['HOD', 'EXAMSECTION'])): ?>
            <div class="col mb-4">
                <div class="bg-success p-4 rounded text-center h-100 d-flex align-items-center justify-content-center">
                    <h5><a href="book_hall.php" class="text-white text-decoration-none">Book Hall</a></h5>
                </div>
            </div>
            <?php endif; ?>
            <?php if (in_array($user['role'], ['ADMINISTRATIVE', 'PRINCIPAL'])): ?>
            <div class="col mb-4">
                <div class="bg-warning p-4 rounded text-center h-100 d-flex align-items-center justify-content-center">
                    <h5><a href="approve_request.php" class="text-white text-decoration-none">View Requests</a></h5>
                </div>
            </div>
            <div class="col mb-4">
                <div class="bg-danger p-4 rounded text-center h-100 d-flex align-items-center justify-content-center">
                    <h5><a href="registered_users.php" class="text-white text-decoration-none">Registered Users</a></h5>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($user['role'] == 'ADMINISTRATIVE'): ?>
            <div class="col mb-4">
                <div class="bg-primary p-4 rounded text-center h-100 d-flex align-items-center justify-content-center">
                    <h5><a href="register.php" class="text-white text-decoration-none">Register User</a></h5>
                </div>
            </div>
            <?php endif; ?>
            <div class="col mb-4">
                <div class="bg-secondary p-4 rounded text-center h-100 d-flex align-items-center justify-content-center">
                    <h5><a href="view_profile.php" class="text-white text-decoration-none">View Profile</a></h5>
                </div>
            </div>
            <?php if (in_array($user['role'], ['HOD', 'EXAMSECTION'])): ?>
            <div class="col mb-4">
                <div class="bg-info p-4 rounded text-center h-100 d-flex align-items-center justify-content-center">
                    <h5><a href="view_bookings.php" class="text-white text-decoration-none">My Bookings</a></h5>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
    
          <!-- /.card -->
        </div>


      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->


  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    
    Developed by Nischal Baidar
   
    
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/adminlte.min.js"></script>

<!-- OPTIONAL SCRIPTS -->
<!-- ChartJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

<!-- AdminLTE for demo purposes -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/js/demo.js"></script>

<!-- Page specific script -->
<script>
    $(function () {
        // Add your custom JavaScript here
    });
</script>

</body>
</html>
