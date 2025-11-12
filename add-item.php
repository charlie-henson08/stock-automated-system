<?php
include __DIR__ . '/assets/database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sku          = $_POST['sku'];
    $name         = $_POST['name'];
    $category     = $_POST['category'];
    $supplier     = $_POST['supplier'];
    $leadTime     = (int)$_POST['leadTime'];
    $reorderPoint = (int)$_POST['reorderPoint'];
    $stock        = (int)$_POST['stock'];

    $stmt = $conn->prepare("INSERT INTO items (sku,name,category,supplier,leadTime,reorderPoint,stock) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("sssiiii", $sku, $name, $category, $supplier, $leadTime, $reorderPoint, $stock);

    if ($stmt->execute()) {
        echo "<script>alert('Item added successfully!'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error adding item: {$conn->error}');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Item</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/styles.css">
  <script src="assets/scripts/validate.js" defer></script>
</head>
<body>
  <header class="app-header">
    <h1>Add New Item</h1>
  </header>

  <main class="grid">
    <section class="card">
      <div class="card-header">
        <h2>Item Details</h2>
      </div>
      <div class="form-wrap">
        <form method="post" id="itemForm">
          <div class="form-row">
            <label>SKU</label>
            <input type="text" name="sku" required>
          </div>
          <div class="form-row">
            <label>Product name</label>
            <input type="text" name="name" required>
          </div>
          <div class="form-row">
            <label>Category</label>
            <input type="text" name="category">
          </div>
          <div class="form-row">
            <label>Supplier</label>
            <input type="text" name="supplier">
          </div>
          <div class="form-row two-col">
            <div>
              <label>Lead time (days)</label>
              <input type="number" name="leadTime" min="0">
            </div>
            <div>
              <label>Reorder point</label>
              <input type="number" name="reorderPoint" min="0">
            </div>
          </div>
          <div class="form-row">
            <label>Initial stock</label>
            <input type="number" name="stock" min="0">
          </div>
          <div class="form-actions">
            <button type="submit">Save Item</button>
            <a href="index.php" class="btn">Cancel</a>
          </div>
        </form>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="app-footer">
    <small>© 2025 Charlie Henson</small>
  </footer>
</body>
</html>
