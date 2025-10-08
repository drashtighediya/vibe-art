<?php
$title = isset($_GET['title']) ? $_GET['title'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';
$image = isset($_GET['image']) ? $_GET['image'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Now - Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="payment-page">
<div class="container mt-5" style="max-width:800px;margin:auto; background:#fff; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,0.08); padding:24px; position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);">
    <h2 class="mb-4">Buy Now - Payment</h2>
    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo htmlspecialchars($image); ?>" alt="Artwork" style="max-width:100%;height:auto;">
        </div>
        <div class="col-md-6">
            <h3><?php echo htmlspecialchars($title); ?></h3>
            <p class="price">Price: ₹<?php echo htmlspecialchars($price); ?></p>
            <form action="process_payment.php" method="POST">
                <input type="hidden" name="title" value="<?php echo htmlspecialchars($title); ?>">
                <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">
                <input type="hidden" name="image" value="<?php echo htmlspecialchars($image); ?>">

                <div class="mb-3">
                    <label for="name" class="form-label" style="font-weight:bold;">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label" style="font-weight:bold;">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label" style="font-weight:bold;">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter your address" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="payment" class="form-label" style="font-weight:bold;">Payment Method</label>
                    <select class="form-control" id="payment" name="payment" required>
                        <option value="COD">Cash on Delivery</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Pay & Order</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
