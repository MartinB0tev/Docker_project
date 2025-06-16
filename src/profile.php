<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

$connection = new PDO("mysql:host=db;dbname=mydb", "root", "example");
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get user info
$stmt = $connection->prepare("SELECT username, email, profile_image FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's products
$stmt = $connection->prepare("SELECT * FROM product_info WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();

// Handle image upload
if (isset($_POST['upload_image']) && isset($_FILES['profile_image'])) {
    $image = $_FILES['profile_image'];
    $imagePath = 'uploads/' . basename($image['name']);
    $imageFileType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    if (!empty($image['tmp_name']) && is_uploaded_file($image['tmp_name'])) {
      $check = getimagesize($image['tmp_name']);
      if ($check === false) {
          echo "File is not an image.";
      } else {
          // Check file size (5MB max)
        if ($image['size'] > 5000000) {
          echo "Sorry, your file is too large.";
      } else {
          // Allow certain file formats
          if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif') {
              echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
          } else {
              // Move the file to the 'uploads' folder
              if (move_uploaded_file($image['tmp_name'], $imagePath)) {
                  // Update the user's profile with the image path
                  $stmt = $connection->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                  $stmt->execute([$imagePath, $_SESSION['user_id']]);
                  echo "The file has been uploaded.";
                  header("Location: profile.php"); // Refresh the page to show the new image
                  exit;
              } else {
                  echo "Sorry, there was an error uploading your file.";
              }
          }
      }
  }
}
}

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
  <h3>User Profile</h3>

  <!-- Display Profile Image -->
  <div>
    <?php if ($user['profile_image']): ?>
        <img src="<?= $user['profile_image'] ?>" class="img-fluid rounded-circle" alt="Profile Image" width="150">
    <?php else: ?>
        <img src="default-profile.jpg" class="img-fluid rounded-circle" alt="Default Profile Image" width="150">
    <?php endif; ?>
  </div>

  <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
  <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

  <hr>

  <!-- Image Upload Form -->
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="profile_image" class="form-label">Upload Profile Image</label>
      <input type="file" class="form-control" name="profile_image" id="profile_image" accept="image/*">
    </div>
    <button type="submit" name="upload_image" class="btn btn-primary">Upload Image</button>
  </form>

  <hr>

  <h4>Your Products</h4>
  <div class="row">
    <?php foreach ($products as $product): ?>
      <div class="col-md-4">
        <div class="card mb-3">
          <?php if ($product['image_path']): ?>
            <img src="<?= $product['image_path'] ?>" class="card-img-top">
          <?php endif; ?>
          <div class="card-body">
            <h5><?= htmlspecialchars($product['name']) ?></h5>
            <p><?= htmlspecialchars($product['description']) ?></p>
            <p>$<?= number_format($product['price'], 2) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
