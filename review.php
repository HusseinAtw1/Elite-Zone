<?php
ob_start();
include 'header.php';

if (!$isLoggedIn) {
    header('location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

function hasAcceptedOrder($userId) {
    $conn = connectDB();
    $query = "SELECT COUNT(*) as order_count FROM orders WHERE ID = ? AND status = 'completed'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['order_count'] >= 1;
}

$canReview = hasAcceptedOrder($userId);

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $canReview) {
    $token = md5(uniqid(rand(), true));
    $_SESSION['review_token'] = $token;

    $title = $_POST['title'];
    $reviewText = $_POST['review_text'];
    $starRating = $_POST['star_rating'];

    $conn = connectDB();
    $query = "INSERT INTO reviews (ID, title, review_text, star_rating, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issd", $userId, $title, $reviewText, $starRating);

    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Your review has been submitted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">There was an error submitting your review. Please try again.</div>';
    }
    
    header("Location: review.php?status=success&token=$token");
    exit();
}


if (isset($_GET['status']) && $_GET['status'] == 'success' && isset($_GET['token']) && isset($_SESSION['review_token']) && $_GET['token'] == $_SESSION['review_token']) {
    $message = '<div class="alert alert-success">Your review has been submitted successfully!</div>';
    unset($_SESSION['review_token']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit a Review</title>

    <style>
        .star-rating {
            direction: rtl;
            display: inline-block;
            padding: 20px;
        }
        .star-rating input[type="radio"] {
            display: none;
        }
        .star-rating label {
            color: #bbb;
            font-size: 18px;
            padding: 0;
            cursor: pointer;
            -webkit-transition: all .3s ease-in-out;
            transition: all .3s ease-in-out;
        }
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input[type="radio"]:checked ~ label {
            color: #f2b600;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Submit a Review</h1>
        
        <?php 
        echo $message;
        
        if (!$canReview): ?>
            <div class="alert alert-warning" role="alert">
                You are not allowed to submit a review unless you have at least one accepted order.
            </div>
        <?php else: ?>
            <form id="reviewForm" action="" method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Review Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="review_text" class="form-label">Review Text</label>
                    <textarea class="form-control" id="review_text" name="review_text" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Star Rating</label>
                    <div class="star-rating">
                        <input type="radio" id="star5" name="star_rating" value="5" required/><label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star4.5" name="star_rating" value="4.5"/><label for="star4.5" title="4.5 stars"><i class="fas fa-star-half-alt"></i></label>
                        <input type="radio" id="star4" name="star_rating" value="4"/><label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star3.5" name="star_rating" value="3.5"/><label for="star3.5" title="3.5 stars"><i class="fas fa-star-half-alt"></i></label>
                        <input type="radio" id="star3" name="star_rating" value="3"/><label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star2.5" name="star_rating" value="2.5"/><label for="star2.5" title="2.5 stars"><i class="fas fa-star-half-alt"></i></label>
                        <input type="radio" id="star2" name="star_rating" value="2"/><label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star1.5" name="star_rating" value="1.5"/><label for="star1.5" title="1.5 stars"><i class="fas fa-star-half-alt"></i></label>
                        <input type="radio" id="star1" name="star_rating" value="1"/><label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                        
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
        <?php endif; ?>
    </div>

  
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reviewForm');
            if (form) {
                form.addEventListener('submit', function(event) {
                    if (!confirm('Are you sure you want to submit this review?')) {
                        event.preventDefault();
                    }
                });
            }
        });
    </script>
    <script src="footer.js"></script>
</body>
</html>

<?php ob_end_flush(); ?>