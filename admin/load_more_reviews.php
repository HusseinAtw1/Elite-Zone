<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['offset'])) {
    include '../functions.php';
    $conn = connectDB();

    $entries_per_page = 10;
    $offset = intval($_GET['offset']);

    $query = "SELECT reviews.*, accounts.Name FROM reviews JOIN accounts ON reviews.ID = accounts.ID ORDER BY reviews.created_at DESC LIMIT $offset, $entries_per_page";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['review_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['review_text']) . "</td>";
        echo "<td>" . $row['star_rating'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
}
?>  