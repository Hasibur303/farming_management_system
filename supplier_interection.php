<?php
require_once __DIR__ . '/security/bootstrap.php';
require_once __DIR__ . '/database.php';
$result = $conn->query('SELECT s.supply_id AS id, u.name AS supplier_name, s.supply_name AS crop_supplied, s.price
                        FROM supplies s JOIN users u ON s.supplier_id = u.user_id
                        ORDER BY s.supply_id DESC');
?>
<!-- Supplier Interaction Page -->
<div class="card">
    <h3>Supplier Interaction</h3>
    <p>Place orders with suppliers and manage your supplier relationships.</p>
    <table>
        <thead>
            <tr>
                <th>Supplier Name</th>
                <th>Crop Supplied</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Fetch supplier interactions -->
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= e($row['supplier_name']) ?></td>
                    <td><?= e($row['crop_supplied']) ?></td>
                    <td><?= e($row['price']) ?></td>
                    <td><a href="buy.php?supply_id=<?= e($row['id']) ?>">Order</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
