<?php
include "connectDB.php";

// 1. Add 'category' column to 'sets' table
$sql_check = "SHOW COLUMNS FROM sets LIKE 'category'";
$result = $conn->query($sql_check);

if ($result->num_rows == 0) {
    $sql_alter = "ALTER TABLE sets ADD COLUMN category VARCHAR(50) DEFAULT 'Genel' AFTER description";
    if ($conn->query($sql_alter) === TRUE) {
        echo "Added 'category' column to 'sets' table.<br>";
    } else {
        echo "Error adding column: " . $conn->error . "<br>";
    }
} else {
    echo "'category' column already exists.<br>";
}

// 2. Create 'favorites' table
$sql_fav = "CREATE TABLE IF NOT EXISTS favorites (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    set_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (set_id) REFERENCES sets(set_id) ON DELETE CASCADE
)";

if ($conn->query($sql_fav) === TRUE) {
    echo "'favorites' table created or already exists.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}
?>
