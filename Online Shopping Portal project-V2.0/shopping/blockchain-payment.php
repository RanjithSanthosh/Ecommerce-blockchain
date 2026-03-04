<?php
session_start();
error_reporting(0);
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:login.php');
} else {
    // Get the current order total for this session/user
    $query = mysqli_query($con, "SELECT SUM(products.productPrice + products.shippingCharge) as total 
                                FROM orders 
                                JOIN products ON orders.productId = products.id 
                                WHERE orders.userId = '" . $_SESSION['id'] . "' 
                                AND orders.paymentMethod = 'Blockchain' 
                                AND (orders.blockchain_txid IS NULL OR orders.blockchain_txid = '')");
    $result = mysqli_fetch_array($query);
    $totalPrice = $result['total'];

    // For demo purposes, convert to a small ETH amount (1 INR/USD = 0.00001 ETH)
    $ethAmount = $totalPrice * 0.00001;
    if ($ethAmount <= 0)
        $ethAmount = 0.001; // Fallback for testing

    // Get the first order ID for the contract reference
    $orderQuery = mysqli_query($con, "SELECT id FROM orders WHERE userId = '" . $_SESSION['id'] . "' AND paymentMethod = 'Blockchain' ORDER BY id DESC LIMIT 1");
    $orderRow = mysqli_fetch_array($orderQuery);
    $orderId = $orderRow['id'];
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Blockchain Payment Gateway</title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/main.css">
        <link rel="stylesheet" href="assets/css/red.css">
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.13.4/ethers.umd.min.js"></script>
        <style>
            .payment-card {
                background: #fff;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                margin-top: 50px;
                text-align: center;
            }

            .eth-icon {
                font-size: 50px;
                color: #627eea;
                margin-bottom: 20px;
            }

            .btn-web3 {
                background: #f6851b;
                color: white;
                border: none;
                padding: 15px 30px;
                font-weight: bold;
                border-radius: 10px;
                transition: 0.3s;
            }

            .btn-web3:hover {
                background: #e2761b;
                transform: translateY(-2px);
            }
        </style>
    </head>

    <body class="cnt-home">
        <header class="header-style-1">
            <?php include('includes/top-header.php'); ?>
            <?php include('includes/main-header.php'); ?>
        </header>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-md-offset-3">
                    <div class="payment-card">
                        <div class="eth-icon"><i class="fa fa-link"></i></div>
                        <h2>Secure Web3 Payment</h2>
                        <p class="text-muted">You are paying for Order #
                            <?php echo $orderId; ?>
                        </p>
                        <hr>
                        <h3 style="color: #333;">Total Amount: <strong>
                                <?php echo $ethAmount; ?> ETH
                            </strong></h3>
                        <p class="text-info"> (~
                            <?php echo $totalPrice; ?> Credits)
                        </p>

                        <div id="payment-status" class="alert alert-warning" style="margin-top:20px;">
                            Connect your MetaMask wallet to proceed.
                        </div>

                        <button id="connectButton" class="btn btn-web3">
                            <i class="fa fa-wallet"></i> Connect MetaMask
                        </button>

                        <button id="payButton" class="btn btn-success btn-lg" style="display:none; width: 100%;">
                            Pay Now via Blockchain
                        </button>

                        <div id="tx-success" style="display:none; margin-top:20px;">
                            <div class="alert alert-success">
                                <h4>Payment Successful!</h4>
                                <p id="tx-hash-display" style="word-break: break-all; font-size: 11px;"></p>
                            </div>
                            <a href="order-history.php" class="btn btn-primary">Go to Order History</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const contractAddress = "0x5FbDB2315678afecb367f032d93F642f64180aa3"; // Local Hardhat Address
            const contractABI = [
                "function payForOrder(uint256 _orderId, bytes32 _orderHash) external payable",
                "event OrderPaid(uint256 indexed orderId, address indexed buyer, uint256 amount, bytes32 orderHash, uint256 timestamp)"
            ];

            let provider, signer, contract;
            const orderId = <?php echo $orderId; ?>;
            const ethPrice = "<?php echo $ethAmount; ?>";
            const targetChainId = "0x7a69"; // 31337 in hex

            async function init() {
                if (typeof window.ethereum !== 'undefined') {
                    document.getElementById('connectButton').onclick = connect;

                    // Listen for network changes
                    window.ethereum.on('chainChanged', () => window.location.reload());
                } else {
                    document.getElementById('payment-status').className = "alert alert-danger";
                    document.getElementById('payment-status').innerText = "MetaMask not found! Please install the MetaMask extension.";
                    document.getElementById('connectButton').disabled = true;
                }
            }

            async function checkNetwork() {
                const chainId = await window.ethereum.request({ method: 'eth_chainId' });
                if (chainId !== targetChainId) {
                    try {
                        await window.ethereum.request({
                            method: 'wallet_switchEthereumChain',
                            params: [{ chainId: targetChainId }],
                        });
                        return true;
                    } catch (switchError) {
                        // This error code indicates that the chain has not been added to MetaMask.
                        if (switchError.code === 4902) {
                            try {
                                await window.ethereum.request({
                                    method: 'wallet_addEthereumChain',
                                    params: [{
                                        chainId: targetChainId,
                                        chainName: 'Hardhat Localhost',
                                        nativeCurrency: { name: 'ETH', symbol: 'ETH', decimals: 18 },
                                        rpcUrls: ['http://127.0.0.1:8545'],
                                    }],
                                });
                                return true;
                            } catch (addError) {
                                document.getElementById('payment-status').className = "alert alert-danger";
                                document.getElementById('payment-status').innerHTML = "<strong>Network Error!</strong> Failed to add the Localhost network.";
                                return false;
                            }
                        }

                        document.getElementById('payment-status').className = "alert alert-danger";
                        document.getElementById('payment-status').innerHTML = "<strong>Wrong Network!</strong> Please switch to Localhost 8545 (Chain ID 31337) in MetaMask.";
                        return false;
                    }
                }
                return true;
            }

            async function connect() {
                try {
                    const isCorrectNetwork = await checkNetwork();
                    if (!isCorrectNetwork) return;

                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    provider = new ethers.BrowserProvider(window.ethereum);
                    signer = await provider.getSigner();
                    contract = new ethers.Contract(contractAddress, contractABI, signer);

                    // Check Balance
                    const balance = await provider.getBalance(accounts[0]);
                    const balanceInEth = ethers.formatEther(balance);

                    console.log("Connected account:", accounts[0]);
                    console.log("Balance:", balanceInEth, "ETH");

                    if (parseFloat(balanceInEth) < parseFloat(ethPrice)) {
                        document.getElementById('payment-status').className = "alert alert-danger";
                        document.getElementById('payment-status').innerHTML = `<strong>Insufficient Balance!</strong><br>Account: ${accounts[0].substring(0, 10)}...<br>Balance: ${balanceInEth} ETH<br>Required: ${ethPrice} ETH.<br><small>Tip: Import a Hardhat account key for testing.</small>`;
                    } else {
                        document.getElementById('connectButton').style.display = 'none';
                        document.getElementById('payButton').style.display = 'block';
                        document.getElementById('payment-status').className = "alert alert-success";
                        document.getElementById('payment-status').innerText = "Wallet Connected! Balance: " + parseFloat(balanceInEth).toFixed(4) + " ETH";
                    }

                    document.getElementById('payButton').onclick = handlePayment;
                } catch (error) {
                    console.error("Connection Error:", error);
                    alert("Connection failed: " + error.message);
                }
            }

            async function handlePayment() {
                try {
                    document.getElementById('payButton').disabled = true;
                    document.getElementById('payButton').innerText = "Confirming in MetaMask...";

                    const amount = ethers.parseEther(ethPrice);
                    const orderHash = ethers.keccak256(ethers.toUtf8Bytes("Order-" + orderId));

                    console.log("Initiating payment for Order:", orderId);
                    console.log("Order Hash:", orderHash);
                    console.log("Value (Wei):", amount.toString());

                    const tx = await contract.payForOrder(orderId, orderHash, {
                        value: amount,
                        gasLimit: 300000 // Force gas limit for local node reliability
                    });

                    document.getElementById('payment-status').className = "alert alert-info";
                    document.getElementById('payment-status').innerText = "Transaction Sent! Waiting for confirmation...";

                    const receipt = await tx.wait();
                    console.log("Transaction Receipt:", receipt);

                    if (receipt.status === 1) {
                        // Update database via AJAX
                        const formData = new FormData();
                        formData.append('orderId', orderId);
                        formData.append('txHash', tx.hash);

                        await fetch('update-blockchain-status.php', {
                            method: 'POST',
                            body: formData
                        });

                        document.getElementById('payButton').style.display = 'none';
                        document.getElementById('payment-status').style.display = 'none';
                        document.getElementById('tx-success').style.display = 'block';
                        document.getElementById('tx-hash-display').innerText = "Transaction Hash: " + tx.hash;
                    } else {
                        throw new Error("Transaction reverted on-chain.");
                    }

                } catch (error) {
                    console.error("Payment error:", error);
                    let msg = error.message;
                    if (error.code === 'ACTION_REJECTED') msg = "User rejected the transaction.";

                    document.getElementById('payment-status').className = "alert alert-danger";
                    document.getElementById('payment-status').innerText = "Payment Error: " + msg;
                    document.getElementById('payButton').disabled = false;
                    document.getElementById('payButton').innerText = "Retry Payment via Blockchain";
                }
            }

            init();
        </script>
    </body>

    </html>
<?php } ?>