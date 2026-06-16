<?php
header('Content-Type: text/plain');

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'college_erp';

try {
    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Successfully connected to the database!\n\n";

    // 1. Inspect users table columns
    echo "--- Users Table Columns ---\n";
    $q = $dbh->query("DESCRIBE users");
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        echo "Field: {$row['Field']} | Type: {$row['Type']} | Null: {$row['Null']}\n";
    }
    echo "\n";

    // 2. Inspect messages table columns
    echo "--- Messages Table Columns ---\n";
    $q = $dbh->query("DESCRIBE messages");
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        echo "Field: {$row['Field']} | Type: {$row['Type']} | Null: {$row['Null']}\n";
    }
    echo "\n";

    // 3. Inspect messages data
    echo "--- Messages Table Content ---\n";
    $q = $dbh->query("SELECT * FROM messages ORDER BY id DESC LIMIT 10");
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: {$row['id']} | Sender: {$row['sender_id']} | Receiver: {$row['receiver_id']} | Text: {$row['message_text']} | Read: {$row['is_read']}\n";
    }
    echo "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
