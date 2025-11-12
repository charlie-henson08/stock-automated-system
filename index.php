<?php
include __DIR__ . '/assets/database/database.php';

if (isset($_GET['delete'])) {
  $sku = $conn->real_escape_string($_GET['delete']);
  $conn->query("DELETE FROM items WHERE sku='$sku'");
  echo "<script>alert('Item deleted successfully!'); window.location='index.php';</script>";
  exit;
}

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM items";
if ($search) {
  $search = $conn->real_escape_string($search);
  $sql .= " WHERE sku LIKE '%$search%' OR name LIKE '%$search%' OR category LIKE '%$search%' OR supplier LIKE '%$search%'";
}
$result = $conn->query($sql);

$items = [];
while ($row = $result->fetch_assoc()) {
  $items[] = $row;
}
$totalSkus = count($items);
$totalStock = array_sum(array_column($items, 'stock'));
$lowStock = count(array_filter($items, fn($i) => $i['stock'] <= $i['reorderPoint']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Automated Stock Control</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <header class="app-header">
    <h1>Automated Stock Control</h1>
    <div class="kpis">
      <div class="kpi">
        <span id="kpi-total-skus"><?= $totalSkus ?></span>
        <label>SKUs</label>
      </div>
      <div class="kpi">
        <span id="kpi-total-stock"><?= $totalStock ?></span>
        <label>Total Stock</label>
      </div>
      <div class="kpi">
        <span id="kpi-low-stock"><?= $lowStock ?></span>
        <label>Low Stock Alerts</label>
      </div>
    </div>
  </header>

  <main class="grid">
    <section class="card">
      <div class="card-header">
        <h2>Inventory</h2>
        <div class="actions">
          <form method="get" style="display:inline;">
            <input type="text" name="search" placeholder="Search products, SKU...">
            <button type="submit">Search</button>
          </form>
          <button type="button" onclick="window.location.href='add-item.php'">Add Item</button>
        </div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>SKU</th>
              <th>Product</th>
              <th>Category</th>
              <th>Stock</th>
              <th>Reorder Point</th>
              <th>Supplier</th>
              <th>Lead Time (days)</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $row): 
              $status = "OK";
              if ($row['stock'] == 0) $status = "OUT";
              elseif ($row['stock'] <= $row['reorderPoint']) $status = "LOW";
            ?>
              <tr>
                <td><?= htmlspecialchars($row['sku']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td>
                  <?= $row['stock'] ?>
                  <span class="status <?= strtolower($status) ?>">
                    <?= $status ?>
                  </span>
                </td>
                <td><?= $row['reorderPoint'] ?></td>
                <td><?= htmlspecialchars($row['supplier']) ?></td>
                <td><?= $row['leadTime'] ?></td>
                <td>
                    <button type="button" class="delete-btn red"
                        onclick="if(confirm('Delete <?= htmlspecialchars($row['name']) ?>?')) 
                                window.location='index.php?delete=<?= urlencode($row['sku']) ?>';">
                        ✕
                    </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="card">
      <div class="card-header">
        <h2>Alerts</h2>
      </div>
      <ul id="alerts" class="alerts">
        <?php
        $alerts = 0;
        foreach ($items as $row) {
          if ($row['stock'] <= $row['reorderPoint']) {
            $alerts++;
            $msg = $row['stock']==0 ? "OUT OF STOCK" : "LOW STOCK";
            echo "<li>" . htmlspecialchars($row['name']) . " (SKU " . htmlspecialchars($row['sku']) . ") is {$msg}.</li>";
          }
        }
        if ($alerts === 0) {
          echo "<li>No alerts.</li>";
        }
        ?>
      </ul>
    </section>
  </main>

  <!-- Footer -->
  <footer class="app-footer">
    <small>© 2025 Charlie Henson</small>
  </footer>
</body>
</html>
