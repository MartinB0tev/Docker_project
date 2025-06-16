<?php
session_start();
$conn = new PDO("mysql:host=db;dbname=mydb", "root", "example");
$products = $conn->query("SELECT p.*, u.username FROM product_info p JOIN users u ON p.user_id = u.id")->fetchAll();
?>
<!doctype html>
<html>
<head>
  <title>All Products</title>
  <?php include 'navbar.php'; ?>
</head>
<body class="bg-light">
  <div class="container">
    <h3>All Products</h3>
    <div class="row">
      <?php foreach ($products as $p): ?>
        <div class="col-md-4">
          <div class="card mb-3">
            <?php if ($p['image_path']): ?>
              <img src="<?= $p['image_path'] ?>" class="card-img-top">
            <?php endif; ?>
            <div class="card-body">
              <h5><?= htmlspecialchars($p['name']) ?></h5>
              <p><?= htmlspecialchars($p['description']) ?></p>
              <p>$<?= number_format($p['price'], 2) ?> — <?= $p['username'] ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
