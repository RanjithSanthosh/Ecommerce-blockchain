<?php
// Test MySQL Connection
echo "===========================================<br>";
echo "MySQL Connection Test<br>";
echo "===========================================<br><br>";

// Try connection with localhost
echo "<strong>Attempting connection to localhost...</strong><br>";
$con = @mysqli_connect('localhost', 'root', '', 'shopping', 3307);

if ($con) {
    echo "✓ <span style='color:green;'><strong>SUCCESS!</strong></span> Connected to MySQL<br>";
    echo "Server: " . mysqli_get_server_info($con) . "<br>";
    
    // Test database
    $result = mysqli_query($con, "SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = 'shopping'");
    $row = mysqli_fetch_assoc($result);
    echo "Tables in 'shopping' database: " . $row['count'] . "<br><br>";
    
    mysqli_close($con);
} else {
    echo "✗ <span style='color:red;'><strong>FAILED!</strong></span><br>";
    echo "Error: " . mysqli_connect_error() . "<br><br>";
    
    echo "<strong>Troubleshooting steps:</strong><br>";
    echo "1. Make sure XAMPP MySQL is running<br>";
    echo "2. Try accessing phpMyAdmin at http://localhost/phpmyadmin<br>";
    echo "3. Check if port 3306 is available<br>";
}

echo "<br>===========================================<br>";
echo "<a href='index.php'>← Back to Home</a>";
?>

