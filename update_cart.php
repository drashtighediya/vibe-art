<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']) || !isset($_POST['cart_id']) || !isset($_POST['action'])) {
    header("Location: cart.php");
    exit();
}

$user_id = (int)$_SESSION['user']['id'];
$cart_id = (int)$_POST['cart_id'];
$action = $_POST['action'];

$sql = "SELECT quantity FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_qty = $row['quantity'];
    
    if ($action == 'increase') {
        $new_qty = $current_qty + 1;
    } elseif ($action == 'decrease') {
        $new_qty = max(1, $current_qty - 1); 
    } else {
        header("Location: cart.php");
        exit();
    }
    
    $update_sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("iii", $new_qty, $cart_id, $user_id);
    $stmt->execute();
}

$stmt->close();
$conn->close();

header("Location: cart.php");
exit();

?>
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']) || !isset($_POST['cart_id'])) {
    header("Location: cart.php");
    exit();
}

$user_id = (int)$_SESSION['user']['id'];
$cart_id = (int)$_POST['cart_id'];

$sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: cart.php");
exit();
?>