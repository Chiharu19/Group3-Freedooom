<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Student Requests</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/faculty/faculty_dashboard.css">
  <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
</head>

<body>
  <div class="wrapper">
    <?php include __DIR__ . '/../layouts/faculty_sidebar.php'; ?>

    <main class="main-content">
      <div class="topbar d-flex justify-content-between align-items-center px-4">
        <h4 class="fw-bold m-0">Student Requests</h4>
        <div>
          <a href="index.php?page=faculty-book" class="btn btn-bsu-red btn-sm">Book Room</a>
        </div>
      </div>

      <div class="container mt-4">
        <div class="card card-custom p-3">
          <div id="requests-list">
            <!-- requests populated by JS -->
          </div>
        </div>
      </div>
    </main>
  </div>



  <!-- Logout Confirmation Modal -->


  <!-- Bootstrap & app -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/faculty/app.js?v=<?= time() ?>"></script>
</body>

</html>