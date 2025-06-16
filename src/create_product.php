<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: auth.php"); exit; }
$conn = new PDO("mysql:host=db;dbname=mydb", "root", "example");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$msg = "";

if (isset($_POST['submit'])) {
  $img = "";
  if ($_FILES['image']['error'] === 0) {
    $img = "images/" . uniqid() . "_" . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES["image"]["tmp_name"], $img);
  }

  $stmt = $conn->prepare("INSERT INTO product_info (name, description, price, image_path, user_id) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$_POST['name'], $_POST['description'], $_POST['price'], $img, $_SESSION['user_id']]);
  $msg = "Product added!";
}
?>
<!doctype html>
<html>
<head>
  <title>Create Product</title>
  <?php include 'navbar.php'; ?>
</head>
<body class="bg-light">
  <div class="container">
    <h3>Create Product</h3>
    <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
    <form method="post" enctype="multipart/form-data">
      <input name="name" class="form-control mb-2" placeholder="Name" required>
      <input name="description" class="form-control mb-2" placeholder="Description" required>
      <input name="price" type="number" step="0.01" class="form-control mb-2" placeholder="Price" required>
      <input name="image" type="file" class="form-control mb-3">
      <button name="submit" class="btn btn-primary">Add</button>
    </form>
  </div>
</body>
</html>
