<?php
ob_start();
include 'adminheader.php';

$conn = connectDB();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_done'])) {
    $contact_id = intval($_POST['contact_id']);
    $update_query = "UPDATE contact_us SET done = 1 WHERE contact_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $contact_id);
    $stmt->execute();
    $stmt->close();
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?action=notdone");
    exit();
}

// Determine which contacts to show
$action = isset($_GET['action']) ? $_GET['action'] : 'notdone';
$done_value = ($action === 'done') ? 1 : 0;

// Get total number of entries
$total_query = "SELECT COUNT(*) as total FROM contact_us WHERE done = $done_value";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_entries = $total_row['total'];

// Set entries per page
$entries_per_page = 10;

// Fetch initial entries with appropriate sorting
$order_by = ($action === 'notdone') ? 'ASC' : 'DESC';
$query = "SELECT * FROM contact_us WHERE done = $done_value ORDER BY date $order_by LIMIT $entries_per_page";
$result = $conn->query($query);

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Contact Us - <?php echo ucfirst($action); ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Contact Submissions - <?php echo ucfirst($action); ?></h1>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="contact-table">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Date</th>
                        <?php if ($action === 'notdone'): ?>
                        <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['contact_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                        <td><?php echo $row['date']; ?></td>
                        <?php if ($action === 'notdone'): ?>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="contact_id" value="<?php echo $row['contact_id']; ?>">
                                <button type="submit" name="mark_done" class="btn btn-success btn-sm">Mark as Done</button>
                            </form>
                        </td>
                        <?php endif; ?>
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
                url: 'load_more_contacts.php',
                method: 'GET',
                data: { 
                    offset: offset,
                    action: '<?php echo $action; ?>'
                },
                success: function(response) {
                    $('#contact-table tbody').append(response);
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
