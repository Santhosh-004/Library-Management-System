<?php
$servername = "localhost";  // Replace with your server name
$username = "root";     // Replace with your MySQL username
$password = "";     // Replace with your MySQL password
$dbname = "library";       // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM Books";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<h1>Title: " . $row["title"] . "</h1><br>";
        echo "Author: " . $row["author"] . "<br>";
        echo "Year: " . $row["year"] . "<br>";
        echo "<br>";
    }
}

?>