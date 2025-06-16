<?php
session_start();
$conn = new PDO("mysql:host=db;dbname=mydb", "root", "example");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: auth.php");
  exit;
}

$msg = "";

if (isset($_POST['login'])) {
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$_POST['email']]);
  $user = $stmt->fetch();
  if ($user && password_verify($_POST['password'], $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    header("Location: profile.php");
    exit;
  } else {
    $msg = "Wrong login.";
  }
}

if (isset($_POST['register'])) {
  $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
  $stmt->execute([$_POST['username'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT)]);
  $msg = "Registration complete.";
}
?>
<!doctype html>
<html>
<head>
  <title>Login/Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.hidden { display: none; }</style>
</head>
<body class="bg-light">
  <div class="container mt-4">
    <h3>Login / Register</h3>
    <?php if ($msg): ?><div class="alert alert-info"><?= $msg ?></div><?php endif; ?>

    <div id="login-form">
      <form method="post">
        <input name="email" class="form-control mb-2" placeholder="Email" required>
        <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>
        <button name="login" class="btn btn-primary">Login</button>
        <p class="mt-2">No account? <a href="#" onclick="toggleForms()">Register</a></p>
      </form>
    </div>

    <div id="register-form" class="hidden">
      <form method="post">
        <input name="username" class="form-control mb-2" placeholder="Username" required>
        <input name="email" class="form-control mb-2" placeholder="Email" required>
        <input name="password" type="password" class="form-control mb-2" placeholder="Password" required>
        <button name="register" class="btn btn-success">Register</button>
        <p class="mt-2">Have an account? <a href="#" onclick="toggleForms()">Login</a></p>
      </form>
    </div>
  </div>

<script>
function toggleForms() {
  document.getElementById('login-form').classList.toggle('hidden');
  document.getElementById('register-form').classList.toggle('hidden');
}
</script>
</body>
</html>
