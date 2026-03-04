# Decentralized E-commerce Payment System

## 🌟 Project Overview

This project is an advanced Online Shopping Portal that integrates **Blockchain Technology** to provide a secure, transparent, and decentralized payment gateway. It bridges the gap between traditional e-commerce (Web2) and the decentralized web (Web3).

---

## 🏗️ Architecture: Hybrid Web2 & Web3

- **Web2 Layer (PHP/MySQL)**: Handles product management, user profiles, cart logic, and session management.
- **Web3 Layer (Ethereum/Solidity)**: Handles immutable payment verification, smart contract interactions, and wallet-based authentication.

---

## ⛓️ Blockchain Integration Details

### 1. Purpose & Benefits

- **Immutability**: Payments are recorded on-chain; neither the user nor the admin can delete the transaction proof.
- **Security**: No credit card details are stored. Transactions are signed locally via **MetaMask**.
- **Transparency**: Every purchase can be verified on a blockchain explorer (simulated in this project via Localhost).
- **Trustless**: Eliminates the need for third-party payment processors by using self-executing **Smart Contracts**.

### 2. Libraries & Tools Used

- **Solidity**: Used to write the `EcommerceStore.sol` smart contract.
- **Hardhat**: Development environment to compile, deploy, and run a local blockchain node.
- **Ethers.js (v6)**: The JavaScript library used to interact with the Ethereum blockchain from the browser.
- **MetaMask**: The Web3 wallet used for transaction signing and account management.
- **PHP/cURL**: Used for backend interactions and database syncing.

---

## 🚀 Step-by-Step Setup & Running Guide

### Prerequisites

- **XAMPP/WAMP**: For running PHP and MySQL.
- **Node.js**: For running the Hardhat blockchain node.
- **MetaMask Extension**: Installed in your Chrome/Edge browser.

### Step 1: Start the Local Blockchain

1. Open a terminal in the `blockchain` folder.
2. Run the following command:
   ```bash
   npm start
   ```
   _Note: This terminal will show list of 20 accounts and their private keys. Keep it open._

### Step 2: Deploy the Smart Contract

1. Open a **new** terminal in the `blockchain` folder.
2. Run the deployment script:
   ```bash
   npm run deploy
   ```
3. Copy the **Contract Address** printed in the terminal and ensure it matches the one in `blockchain-payment.php`.

### Step 3: Configure MetaMask

1. Open MetaMask and click **Connect MetaMask** on the website.
2. The site will automatically prompt you to add the **Hardhat Localhost** network (Chain ID 31337). Approve it.
3. To get fake ETH, copy the **Private Key of Account #0** from your `hardhat node` terminal.
4. In MetaMask, go to **Import Account** and paste that Private Key. You will now see **10,000 ETH**.

### Step 4: Run the PHP Web Server

1. Ensure your MySQL is running in XAMPP (Port 3306).
2. Start the PHP server:
   ```bash
   php -S localhost:8080
   ```
3. Visit `http://localhost:8080` in your browser.

---

## ⚙️ How it Works (Technical Flow)

1. **Selection**: User selects 'Blockchain / Crypto' at checkout.
2. **Redirection**: User is sent to `blockchain-payment.php` where their order total is converted to ETH.
3. **Connection**: Browser connects to MetaMask via `ethers.js`.
4. **Transaction**: `contract.payForOrder(orderId, hash)` is called, sending ETH to the Smart Contract.
5. **Confirmation**: Once the block is mined, an AJAX call (via `update-blockchain-status.php`) updates the MySQL database with the **Transaction Hash**.
6. **Validation**: The Admin panel displays the hash as immutable proof of payment.

---

## 👨‍💻 Developed for: Final Year Project

This project demonstrates technical proficiency in full-stack development, smart contract security, and decentralized finance (DeFi) integration.
