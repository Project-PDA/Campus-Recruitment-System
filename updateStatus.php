<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: ".$conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['app_id']) && isset($_POST['status'])) {
        $app_id = intval($_POST['app_id']);
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE app_id = ?");
        $stmt->bind_param("si", $status, $app_id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Missing form data.";
    }
    $conn->close();
    header('Location: company_dash.php');
}
?>
