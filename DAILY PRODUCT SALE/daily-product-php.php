<?php
session_start();
require "/xampp/htdocs/Ease_Slip/assets/connection.php";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the POST data (JSON format)
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Extract the selected date
$date = isset($data['date']) ? $data['date'] : date('Y-m-d'); // Default to today's date

// Query to fetch product and transaction data for the selected date
$sql = "
    SELECT 
        p.productID,
        p.prod_name,
        p.image_path,
        SUM(t.sold) AS total_sold,
        (SUM(t.sold) * p.prod_price) AS total_price
    FROM product p
    LEFT JOIN transaction t ON p.productID = t.productID
    WHERE DATE(t.sold_date) = ?
    GROUP BY p.productID, p.prod_name, p.prod_price, p.image_path
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

if ($result->num_rows > 0) {
    // Fetch data as associative array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Set the content type to JSON
header('Content-Type: application/json');
echo json_encode($data);

// Close the database connection
$conn->close();
?>

