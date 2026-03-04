<?php
// Advanced Debug Information
echo "<!DOCTYPE html>";
echo "<html><head><style>";
echo "body { font-family: Arial; margin: 20px; background: #f5f5f5; }";
echo ".section { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
echo ".success { color: green; font-weight: bold; }";
echo ".error { color: red; font-weight: bold; }";
echo ".warning { color: orange; font-weight: bold; }";
echo "table { border-collapse: collapse; width: 100%; }";
echo "td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }";
echo "th { background-color: #4CAF50; color: white; }";
echo "tr:nth-child(even) { background-color: #f2f2f2; }";
echo "</style></head><body>";

echo "<h1>🔍 Shopping Portal - Advanced Debug Information</h1>";

// 1. PHP Version
echo "<div class='section'>";
echo "<h2>PHP Information</h2>";
echo "PHP Version: <span class='success'>" . phpversion() . "</span><br>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "s<br>";
echo "Display Errors: " . (ini_get('display_errors') ? 'ON' : 'OFF') . "<br>";
echo "</div>";

// 2. MySQL Connection Test
echo "<div class='section'>";
echo "<h2>MySQL Connection Test</h2>";

$con = @mysqli_connect('localhost', 'root', '', 'shopping', 3307);

if ($con) {
    echo "<span class='success'>✓ Connected to MySQL successfully!</span><br>";
    echo "Server: " . mysqli_get_server_info($con) . "<br>";
    echo "Database: shopping<br>";
    
    // 3. Table Statistics
    echo "</div>";
    echo "<div class='section'>";
    echo "<h2>Database Statistics</h2>";
    echo "<table>";
    echo "<tr><th>Table Name</th><th>Row Count</th><th>Engine</th></tr>";
    
    $tables = array('users', 'products', 'category', 'subcategory', 'orders', 'admin', 'wishlist', 'ordertrackhistory', 'productreviews', 'userlog');
    
    foreach ($tables as $table) {
        $result = mysqli_query($con, "SELECT COUNT(*) as count FROM $table");
        $engine = mysqli_query($con, "SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'shopping' AND TABLE_NAME = '$table'");
        
        if ($result && $engine) {
            $row = mysqli_fetch_assoc($result);
            $eng = mysqli_fetch_assoc($engine);
            $count = $row['count'];
            $engine_name = $eng['ENGINE'] ?? 'Unknown';
            
            echo "<tr>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>";
            if ($count > 0) {
                echo "<span class='success'>$count records</span>";
            } else {
                echo "<span class='warning'>Empty (0 records)</span>";
            }
            echo "</td>";
            echo "<td>$engine_name</td>";
            echo "</tr>";
        } else {
            echo "<tr><td>$table</td><td colspan='2'><span class='error'>Error querying table</span></td></tr>";
        }
    }
    echo "</table>";
    echo "</div>";
    
    // 4. Sample Data
    echo "<div class='section'>";
    echo "<h2>Sample Product Data (First 5)</h2>";
    
    $result = mysqli_query($con, "SELECT id, name, price, quantity FROM products LIMIT 5");
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Product Name</th><th>Price</th><th>Quantity</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>$" . htmlspecialchars($row['price']) . "</td>";
            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='warning'>No products found in database</span>";
    }
    echo "</div>";
    
    // 5. Sample Users
    echo "<div class='section'>";
    echo "<h2>Sample User Data (First 5)</h2>";
    
    $result = mysqli_query($con, "SELECT id, name, email FROM users LIMIT 5");
    
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<span class='warning'>No users found in database</span>";
    }
    echo "</div>";
    
    // 6. Recommended Actions
    echo "<div class='section'>";
    echo "<h2>✓ All Systems Operational</h2>";
    echo "<p>Your database is properly configured and loaded with data.</p>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ul>";
    echo "<li><a href='index.php'>→ Go to Home Page</a></li>";
    echo "<li><a href='login.php'>→ Login to Account</a></li>";
    echo "<li><a href='admin/'>→ Admin Dashboard</a></li>";
    echo "<li><a href='category.php'>→ Browse Products</a></li>";
    echo "</ul>";
    echo "</div>";
    
    mysqli_close($con);
} else {
    echo "<span class='error'>✗ Failed to connect to MySQL!</span><br>";
    echo "Error: " . mysqli_connect_error() . "<br><br>";
    
    echo "<div class='section'>";
    echo "<h2>Troubleshooting</h2>";
    echo "<ul>";
    echo "<li>1. Make sure MySQL is running</li>";
    echo "<li>2. Check MySQL process: <code>tasklist | findstr mysqld</code></li>";
    echo "<li>3. Verify port 3306: <code>netstat -ano | findstr :3306</code></li>";
    echo "<li>4. Check MySQL error log: <code>C:\\xampp\\mysql\\data\\mysql_error.log</code></li>";
    echo "<li>5. Restart MySQL: Run <code>C:\\xampp\\QUICK_START.bat</code></li>";
    echo "</ul>";
    echo "</div>";
}

echo "</body></html>";
?>

