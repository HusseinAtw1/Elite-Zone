<?php
include '../functions.php';
$conn = connectDB();

$entries_per_page = 10;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : 'notdone';
$done_value = ($action === 'done') ? 1 : 0;

// Determine the order
$order_by = ($action === 'notdone') ? 'ASC' : 'DESC';

$query = "SELECT * FROM contact_us WHERE done = $done_value ORDER BY date $order_by LIMIT $offset, $entries_per_page";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['contact_id'] . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
    echo "<td>" . htmlspecialchars($row['message']) . "</td>";
    echo "<td>" . $row['date'] . "</td>";
    if ($action === 'notdone') {
        echo "<td>
                <form method='POST' action=''>
                    <input type='hidden' name='contact_id' value='" . $row['contact_id'] . "'>
                    <button type='submit' name='mark_done' class='btn btn-success btn-sm'>Mark as Done</button>
                </form>
              </td>";
    }
    echo "</tr>";
}
?>
