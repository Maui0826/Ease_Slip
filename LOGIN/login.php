<?php
session_start();
require "/xampp/htdocs/Ease_Slip/assets/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        echo "Please fill in both fields.";
        exit;
    }

    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username_id'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $username === 'admin' ? 'admin' : 'user'; // Pass role to the session
            
            if ($username === 'admin') {
                header("Location: /DashboardMenu/dashboardM.html");
            } else {
                header("Location: /POS/billing.html");
            }
            exit;
        } else {
            echo "Incorrect password. Please try again.";
        }
    } else {
        echo "No account found with the provided username.";
    }

    $stmt->close();
}

$conn->close();
?>
