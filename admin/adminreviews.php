<?php
ob_start();
include 'adminheader.php';

$conn = connectDB();

// Set entries per page
$entries_per_page = 10;

// Get total number of entries
$total_query = "SELECT COUNT(*) as total FROM reviews";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_entries = $total_row['total'];

// Fetch initial entries with user names
$query = "SELECT reviews.*, accounts.Name FROM reviews JOIN accounts ON reviews.ID = accounts.ID ORDER BY reviews.created_at DESC LIMIT $entries_per_page";
$result = $conn->query($query);

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reviews</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Reviews</h1>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="reviews-table">
                <thead class="table-dark">
                    <tr>
                        <th>Review ID</th>
                        <th>User Name</th>
                        <th>Title</th>
                        <th>Review Text</th>
                        <th>Star Rating</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['review_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['review_text']); ?></td>
                        <td><?php echo $row['star_rating']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_entries > $entries_per_page): ?>
            <button id="load-more" class="btn btn-primary mt-3" data-offset="<?php echo $entries_per_page; ?>">Load More</button>
        <?php endif; ?>
    </div>

    <script>
    $(document).ready(function() {
        $('#load-more').click(function() {
            var offset = $(this).data('offset');
            $.ajax({
                url: 'load_more_reviews.php',
                method: 'GET',
                data: { offset: offset },
                success: function(response) {
                    $('#reviews-table tbody').append(response);
                    var newOffset = offset + <?php echo $entries_per_page; ?>;
                    $('#load-more').data('offset', newOffset);
                    if (newOffset >= <?php echo $total_entries; ?>) {
                        $('#load-more').hide();
                    }
                }
            });
        });
    });
    </script>
</body>
</html>


