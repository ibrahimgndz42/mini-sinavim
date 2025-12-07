<?php
include "session_check.php";
include "connectDB.php";

if (isset($_GET['id'])) {
    $set_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Favori kontrolü
    $sql_check = "SELECT * FROM favorites WHERE user_id = $user_id AND set_id = $set_id";
    $result = $conn->query($sql_check);

    if ($result->num_rows > 0) {
        // Zaten favori -> Kaldır
        $conn->query("DELETE FROM favorites WHERE user_id = $user_id AND set_id = $set_id");
        $msg = "removed";
    } else {
        // Favori değil -> Ekle
        $conn->query("INSERT INTO favorites (user_id, set_id) VALUES ('$user_id', '$set_id')");
        $msg = "added";
    }
    
    header("Location: view_set.php?id=$set_id");
    exit;

} else {
    echo "ID gerekli.";
    exit;
}
?>
