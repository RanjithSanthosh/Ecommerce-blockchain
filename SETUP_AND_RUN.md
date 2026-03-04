# 🏃 Setup & Running Process

This guide covers all the dependencies and the step-by-step process required to run the Hybrid E-commerce Web3 Application locally.

## 📦 Required Dependencies to Install

Before running this project, ensure you have the following installed on your laptop/PC:

1. **[XAMPP](https://www.apachefriends.org/index.html) or WAMP Server**: To run Apache and MySQL.
2. **[Node.js](https://nodejs.org/)**: To run npm packages, Hardhat, and local Ethereum node execution.
3. **[MetaMask Wallet Extension](https://metamask.io/)**: Installed on Google Chrome/Brave/Edge to interact with Web3.

---

## 🚀 Step 1: Start the Local Ethereum Blockchain

Since you're running this locally, you don't need real cryptocurrency. You will start a **simulated blockchain node** that generates fake accounts with 10,000 test ETH.

1. Open your terminal (Command Prompt/PowerShell).
2. Navigate to the blockchain module:
   ```bash
   cd "Blockchain-Smart-Contracts\blockchain"
   ```
3. Install dependencies (if running for the first time):
   ```bash
   npm install
   ```
4. Start the Hardhat Node:
   ```bash
   npm start
   ```
   _(Keep this terminal open! A list of 20 accounts will appear. You will need one of their Private Keys for MetaMask)._

---

## 📜 Step 2: Deploy the Smart Contract

You need to upload the `EcommerceStore.sol` smart contract onto your new local node.

1. Open a **second** terminal window.
2. Navigate to the exact same directory:
   ```bash
   cd "Blockchain-Smart-Contracts\blockchain"
   ```
3. Run the deployment script:
   ```bash
   npm run deploy
   ```
4. **Copy the Contract Address** from the console output. Verify that this address matches the `contractAddress` listed at the top of the file:
   `Online Shopping Portal project-V2.0\shopping\blockchain-payment.php`

---

## 🐘 Step 3: Configure Database and Run PHP Server

Now you start the traditional e-commerce Web2 application.

1. Open **XAMPP Control Panel** and Start both **Apache** and **MySQL** (Ensure Port is 3306).
2. Go to `http://localhost/phpmyadmin` in your browser.
3. Import the SQL file located at: `Online Shopping Portal project-V2.0\SQL file\shopping.sql`.
4. Open a **third** terminal window.
5. Navigate into the shopping app folder:
   ```bash
   cd "Online Shopping Portal project-V2.0\shopping"
   ```
6. Start the PHP Built-in Server:
   ```bash
   php -S localhost:8080
   ```

---

## 🔗 Step 4: Using MetaMask and Testing the Payment

1. Open Chrome and go to: `http://localhost:8080/shopping/index.php`
2. Open your **MetaMask Extension** and ensure you're logged in.
3. Click on the MetaMask **Network list** at the top left. Select **Add Network** -> **Add a network manually**:
   - Network name: `Hardhat Localhost`
   - New RPC URL: `http://127.0.0.1:8545`
   - Chain ID: `31337`
   - Currency symbol: `ETH`
4. Click **Import Account** in MetaMask and paste the `Private Key` from **Account 0** shown in your first terminal. (You will get $10,000 fake ETH).
5. Open the Shopping site, log in to an account, add a product to the cart, check out via **"Blockchain / Crypto"**.
6. The site will trigger MetaMask cleanly and automatically update your SQL Database once the transaction finishes!
