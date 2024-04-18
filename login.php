<?php
include __DIR__."/services/envs.php";
$error = "";

// Post request
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve form data
  $password = $_POST["password"] ?? '';

  if ($password == $env_variables["PASSWORD"]) {
    session_start();
    $_SESSION["authenticated"] = true;
    header('Location: dashboard.php');
    exit;
  } else {
    $error = "Wrong Password";
  }
}

// Set page title
$pageTitle = "Login";

// Set page content
ob_start();
?>
<div class="container">
  <!-- Outer Row -->
  <div class="row justify-content-center">
    <div class="col-xl-10 col-lg-12 col-md-9">
      <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
          <!-- Nested Row within Card Body -->
          <div class="row">
            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
            <div class="col-lg-6">
              <div class="p-5">
                <div class="text-center">
                  <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                </div>
                <form class="user" action="" method="POST">
                  <div class="form-group">
                    <input type="password" name="password" class="form-control form-control-user" placeholder="Password">
                  </div>
                  <button type="submit" class="btn btn-primary btn-user btn-block">
                    Login
                  </button>
                  <p class="text-danger text-center mt-2"><?= $error ?></p>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
// Set content file
$contentView = ob_get_clean();

// Include login layout
include __DIR__."/layouts/default.php";
?>