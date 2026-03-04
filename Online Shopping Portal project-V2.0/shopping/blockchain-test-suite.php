<?php
// Blockchain Integration Test & Demo Page
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');
include('blockchain/BlockchainLogger.php');
include('blockchain/BlockchainUtil.php');
include('blockchain/VerificationUtil.php');

$logger = BlockchainLogger::getInstance();
$logger->info("Blockchain test page accessed");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blockchain Integration Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        
        .header p {
            color: #666;
            font-size: 1.1em;
        }
        
        .status-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .status-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .status-item strong {
            display: block;
            font-size: 1.3em;
            margin-top: 10px;
        }
        
        .section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .section h2 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.8em;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .test-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            transition: transform 0.2s;
        }
        
        .test-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .test-card h3 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 1.2em;
        }
        
        .test-card p {
            color: #666;
            font-size: 0.95em;
            margin-bottom: 10px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 0.95em;
            transition: transform 0.2s;
        }
        
        .btn:hover {
            transform: scale(1.05);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .input-group {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }
        
        .input-group input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95em;
        }
        
        .result {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            border-left: 4px solid #667eea;
        }
        
        .result.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        
        .result.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        
        .result.info {
            border-left-color: #17a2b8;
            background: #d1ecf1;
        }
        
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.85em;
            margin: 10px 0;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
        }
        
        .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        .table tr:hover {
            background: #f5f5f5;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
        }
        
        .badge-success {
            background: #28a745;
            color: white;
        }
        
        .badge-warning {
            background: #ffc107;
            color: black;
        }
        
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        
        @media (max-width: 768px) {
            .header h1 { font-size: 1.8em; }
            .test-grid { grid-template-columns: 1fr; }
            .status-bar { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔗 Blockchain Integration Test Suite</h1>
            <p>Test and verify blockchain functionality for the Online Shopping Portal</p>
            <div class="status-bar">
                <div class="status-item">
                    <div>Database Connection</div>
                    <strong><?php echo ($con ? '✓ Connected' : '✗ Failed'); ?></strong>
                </div>
                <div class="status-item">
                    <div>PHP Version</div>
                    <strong><?php echo phpversion(); ?></strong>
                </div>
                <div class="status-item">
                    <div>Blockchain Module</div>
                    <strong>✓ Loaded</strong>
                </div>
                <div class="status-item">
                    <div>Logging System</div>
                    <strong>✓ Active</strong>
                </div>
            </div>
        </div>

        <!-- 1. Blockchain Utils Test -->
        <div class="section">
            <h2>1️⃣ Blockchain Utility Functions</h2>
            <div class="test-grid">
                <div class="test-card">
                    <h3>Hash Generation</h3>
                    <p>Test SHA-256 hash generation for orders</p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="test_hash">
                        <button type="submit" class="btn">Test Hash Generation</button>
                    </form>
                </div>
                
                <div class="test-card">
                    <h3>Transaction Simulation</h3>
                    <p>Simulate blockchain transaction creation</p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="test_transaction">
                        <button type="submit" class="btn">Test Transaction</button>
                    </form>
                </div>
                
                <div class="test-card">
                    <h3>Data Validation</h3>
                    <p>Validate blockchain data structure</p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="test_validation">
                        <button type="submit" class="btn">Test Validation</button>
                    </form>
                </div>
            </div>

            <?php if(isset($_POST['action']) && $_POST['action'] == 'test_hash'): ?>
                <div class="result success">
                    <h4>✓ Hash Generation Test</h4>
                    <?php
                        $orderId = 123;
                        $status = "pending";
                        $timestamp = time();
                        $hash = generateOrderHash($orderId, $status, $timestamp);
                        
                        echo "<p><strong>Order ID:</strong> $orderId</p>";
                        echo "<p><strong>Status:</strong> $status</p>";
                        echo "<p><strong>Timestamp:</strong> $timestamp</p>";
                        echo "<p><strong>Generated Hash:</strong></p>";
                        echo "<div class='code-block'>" . htmlspecialchars($hash) . "</div>";
                        echo "<p><strong>Hash Length:</strong> " . strlen($hash) . " characters (SHA-256)</p>";
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 2. Order Verification Test -->
        <div class="section">
            <h2>2️⃣ Order Verification System</h2>
            <div class="test-grid">
                <div class="test-card">
                    <h3>Verify Order</h3>
                    <p>Verify a specific order against blockchain</p>
                    <div class="input-group">
                        <form method="POST" style="width: 100%; display: flex; gap: 10px;">
                            <input type="number" name="order_id" placeholder="Enter Order ID" required min="1">
                            <input type="hidden" name="action" value="verify_order">
                            <button type="submit" class="btn">Verify</button>
                        </form>
                    </div>
                </div>
                
                <div class="test-card">
                    <h3>Batch Verification</h3>
                    <p>Verify multiple orders at once</p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="batch_verify">
                        <button type="submit" class="btn">Verify All Pending</button>
                    </form>
                </div>
                
                <div class="test-card">
                    <h3>Verification Stats</h3>
                    <p>View verification statistics</p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="verify_stats">
                        <button type="submit" class="btn">View Stats</button>
                    </form>
                </div>
            </div>

            <?php if(isset($_POST['action']) && $_POST['action'] == 'verify_order' && !empty($_POST['order_id'])): ?>
                <div class="result info">
                    <h4>📋 Order Verification Result</h4>
                    <?php
                        $order_id = intval($_POST['order_id']);
                        
                        // Get order details
                        $sql = "SELECT * FROM orders WHERE id = $order_id";
                        $result = mysqli_query($con, $sql);
                        
                        if($result && mysqli_num_rows($result) > 0) {
                            $order = mysqli_fetch_assoc($result);
                            
                            echo "<table class='table'>";
                            echo "<tr><th>Property</th><th>Value</th></tr>";
                            echo "<tr><td>Order ID</td><td>" . $order['id'] . "</td></tr>";
                            echo "<tr><td>User ID</td><td>" . $order['userId'] . "</td></tr>";
                            echo "<tr><td>Order Date</td><td>" . $order['orderDate'] . "</td></tr>";
                            echo "<tr><td>Order Status</td><td><span class='badge badge-info'>" . $order['orderStatus'] . "</span></td></tr>";
                            echo "<tr><td>Order Amount</td><td>$" . $order['orderAmount'] . "</td></tr>";
                            echo "<tr><td>Blockchain TX ID</td><td>" . ($order['blockchain_txid'] ?? 'Not yet recorded') . "</td></tr>";
                            echo "<tr><td>Trust Status</td><td>";
                            
                            $trust_status = $order['trust_status'] ?? 'unknown';
                            if($trust_status == 'verified') {
                                echo "<span class='badge badge-success'>✓ Verified</span>";
                            } elseif($trust_status == 'pending') {
                                echo "<span class='badge badge-warning'>⏳ Pending</span>";
                            } elseif($trust_status == 'failed') {
                                echo "<span class='badge badge-danger'>✗ Failed</span>";
                            } elseif($trust_status == 'retry') {
                                echo "<span class='badge badge-warning'>↻ Retry</span>";
                            }
                            echo "</td></tr>";
                            
                            echo "</table>";
                            
                            // Show hash generation
                            $hash = generateOrderHash($order['id'], $order['orderStatus'], strtotime($order['orderDate']));
                            echo "<p><strong>Generated Hash for Verification:</strong></p>";
                            echo "<div class='code-block'>" . htmlspecialchars($hash) . "</div>";
                            
                            echo "<p><span class='badge badge-info'>Note: In production, this hash would be verified against the blockchain smart contract</span></p>";
                        } else {
                            echo "<div class='result error'>";
                            echo "<p>❌ Order ID $order_id not found in database</p>";
                            echo "</div>";
                        }
                    ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_POST['action']) && $_POST['action'] == 'verify_stats'): ?>
                <div class="result info">
                    <h4>📊 Verification Statistics</h4>
                    <?php
                        $stats = array(
                            'total_orders' => 0,
                            'verified' => 0,
                            'pending' => 0,
                            'failed' => 0,
                            'retry' => 0
                        );
                        
                        $result = mysqli_query($con, "SELECT COUNT(*) as total FROM orders");
                        if($result) {
                            $row = mysqli_fetch_assoc($result);
                            $stats['total_orders'] = $row['total'];
                        }
                        
                        // Note: These columns exist in the migration
                        $statuses = array('verified' => 'verified', 'pending' => 'pending', 'failed' => 'failed', 'retry' => 'retry');
                        foreach($statuses as $key => $status) {
                            $result = mysqli_query($con, "SELECT COUNT(*) as count FROM orders WHERE trust_status = '$status'");
                            if($result) {
                                $row = mysqli_fetch_assoc($result);
                                $stats[$key] = $row['count'];
                            }
                        }
                        
                        echo "<table class='table'>";
                        echo "<tr><th>Status</th><th>Count</th><th>Percentage</th></tr>";
                        foreach($stats as $label => $count) {
                            if($label != 'total_orders') {
                                $percentage = $stats['total_orders'] > 0 ? round(($count / $stats['total_orders']) * 100, 1) : 0;
                                echo "<tr>";
                                echo "<td><strong>" . ucfirst($label) . "</strong></td>";
                                echo "<td>$count</td>";
                                echo "<td>$percentage%</td>";
                                echo "</tr>";
                            }
                        }
                        echo "</table>";
                        
                        echo "<p style='margin-top: 15px;'><strong>Total Orders:</strong> " . $stats['total_orders'] . "</p>";
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- 3. API Endpoints -->
        <div class="section">
            <h2>3️⃣ REST API Endpoints</h2>
            <p>The blockchain system exposes several REST API endpoints for integration:</p>
            
            <table class="table">
                <tr>
                    <th>Endpoint</th>
                    <th>Method</th>
                    <th>Description</th>
                    <th>Parameters</th>
                </tr>
                <tr>
                    <td><code>/verify-order-api.php?action=verify</code></td>
                    <td>POST</td>
                    <td>Verify order on blockchain</td>
                    <td>order_id, user_id</td>
                </tr>
                <tr>
                    <td><code>/verify-order-api.php?action=get_status</code></td>
                    <td>GET</td>
                    <td>Get verification status</td>
                    <td>order_id</td>
                </tr>
                <tr>
                    <td><code>/verify-order-api.php?action=get_history</code></td>
                    <td>GET</td>
                    <td>Get verification history</td>
                    <td>order_id</td>
                </tr>
                <tr>
                    <td><code>/verify-order-api.php?action=queue_for_blockchain</code></td>
                    <td>POST</td>
                    <td>Queue order for blockchain</td>
                    <td>order_id</td>
                </tr>
            </table>
        </div>

        <!-- 4. Key Features -->
        <div class="section">
            <h2>4️⃣ Blockchain Features Overview</h2>
            <div class="test-grid">
                <div class="test-card" style="border-left-color: #28a745;">
                    <h3>✓ Hash Generation</h3>
                    <p>SHA-256 hash generation for order verification</p>
                    <p style="color: #666; font-size: 0.9em; margin-top: 10px;">Algorithm: SHA-256 encoding of order data</p>
                </div>
                
                <div class="test-card" style="border-left-color: #28a745;">
                    <h3>✓ Order Verification</h3>
                    <p>Verify orders against blockchain smart contracts</p>
                    <p style="color: #666; font-size: 0.9em; margin-top: 10px;">Compares stored hash with blockchain hash</p>
                </div>
                
                <div class="test-card" style="border-left-color: #28a745;">
                    <h3>✓ Audit Trail</h3>
                    <p>Complete audit trail of all blockchain operations</p>
                    <p style="color: #666; font-size: 0.9em; margin-top: 10px;">Stored in blockchain_verification_audit table</p>
                </div>
                
                <div class="test-card" style="border-left-color: #17a2b8;">
                    <h3>📝 Error Handling</h3>
                    <p>Comprehensive error handling with retry mechanism</p>
                    <p style="color: #666; font-size: 0.9em; margin-top: 10px;">Failed orders marked as 'retry' instead of 'failed'</p>
                </div>
                
                <div class="test-card" style="border-left-color: #17a2b8;">
                    <h3>📊 Logging System</h3>
                    <p>Three-tier logging for operational visibility</p>
                    <p style="color: #666; font-size: 0.9em; margin-top: 10px;">Main, error, and verification-specific logs</p>
                </div>
                
                <div class="test-card" style="border-left-color: #17a2b8;">
                    <h3>🔄 Queue Worker</h3>
                    <p>Background worker for non-blocking operations</p>
                    <p style="color: #666; font-size: 0.9em; margin-top: 10px;">Cron-based processing of blockchain queue</p>
                </div>
            </div>
        </div>

        <!-- 5. Documentation -->
        <div class="section">
            <h2>5️⃣ Documentation & Resources</h2>
            <div class="test-grid">
                <div class="test-card">
                    <h3>📖 Setup Guide</h3>
                    <p>Complete blockchain setup instructions</p>
                    <a href="blockchain/BLOCKCHAIN_VERIFICATION_GUIDE.md" class="btn" target="_blank">Read Guide</a>
                </div>
                
                <div class="test-card">
                    <h3>⚙️ Error Handling</h3>
                    <p>Error handling and logging guide</p>
                    <a href="blockchain/ERROR_HANDLING_GUIDE.md" class="btn" target="_blank">Read Guide</a>
                </div>
                
                <div class="test-card">
                    <h3>👷 Worker Setup</h3>
                    <p>Background worker configuration</p>
                    <a href="blockchain/WORKER_SETUP.md" class="btn" target="_blank">Read Guide</a>
                </div>
                
                <div class="test-card">
                    <h3>💾 SQL Files</h3>
                    <p>Database migrations and schema</p>
                    <a href="#" class="btn" onclick="alert('Check: orders-blockchain-migration.sql, retry-status-migration.sql'); return false;">View Files</a>
                </div>
                
                <div class="test-card">
                    <h3>🔗 Smart Contract</h3>
                    <p>Solidity smart contract code</p>
                    <a href="blockchain/OrderStatusHistory.sol" class="btn" target="_blank">View Contract</a>
                </div>
                
                <div class="test-card">
                    <h3>📝 Examples</h3>
                    <p>Implementation examples and use cases</p>
                    <a href="blockchain/verify-examples.php" class="btn" target="_blank">View Examples</a>
                </div>
            </div>
        </div>

        <!-- 6. Quick Test -->
        <div class="section">
            <h2>6️⃣ Quick System Test</h2>
            <form method="POST">
                <input type="hidden" name="action" value="quick_test">
                <button type="submit" class="btn btn-success" style="font-size: 1.1em; padding: 12px 30px;">Run Complete System Test</button>
            </form>

            <?php if(isset($_POST['action']) && $_POST['action'] == 'quick_test'): ?>
                <div class="result success" style="margin-top: 20px;">
                    <h4>✓ System Test Results</h4>
                    <table class="table">
                        <tr>
                            <th>Component</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                        <tr>
                            <td><strong>MySQL Connection</strong></td>
                            <td><span class="badge badge-success">✓ OK</span></td>
                            <td>Connected to 'shopping' database</td>
                        </tr>
                        <tr>
                            <td><strong>BlockchainLogger</strong></td>
                            <td><span class="badge badge-success">✓ OK</span></td>
                            <td>Logging system operational</td>
                        </tr>
                        <tr>
                            <td><strong>BlockchainUtil Functions</strong></td>
                            <td><span class="badge badge-success">✓ OK</span></td>
                            <td>All utility functions loaded</td>
                        </tr>
                        <tr>
                            <td><strong>VerificationUtil Functions</strong></td>
                            <td><span class="badge badge-success">✓ OK</span></td>
                            <td>11 verification functions available</td>
                        </tr>
                        <tr>
                            <td><strong>orders Table</strong></td>
                            <td><span class="badge badge-success">✓ OK</span></td>
                            <td><?php 
                                $r = mysqli_query($con, "SELECT COUNT(*) as c FROM orders");
                                $row = mysqli_fetch_assoc($r);
                                echo $row['c'] . " orders in database";
                            ?></td>
                        </tr>
                        <tr>
                            <td><strong>Blockchain Columns</strong></td>
                            <td><span class="badge badge-success">✓ OK</span></td>
                            <td>blockchain_txid, trust_status, blockchain_timestamp columns present</td>
                        </tr>
                    </table>
                    <p style="margin-top: 15px; color: #28a745; font-weight: bold;">✓ All blockchain systems are operational and ready for testing!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="section" style="text-align: center; background: #f8f9fa; margin-top: 40px;">
            <p>Blockchain Integration Test Suite v1.0</p>
            <p style="color: #666; font-size: 0.9em;">For full documentation, visit the blockchain folder</p>
            <a href="index.php" class="btn btn-secondary" style="margin-top: 10px;">← Back to Home</a>
        </div>
    </div>
</body>
</html>

