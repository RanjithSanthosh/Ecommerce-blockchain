# 🗂️ Project Folder Structure & Roles

This section explicitly breaks down what each major component of the project does and how the distinct technical layers communicate with one another.

---

## 🏗️ 1. `Blockchain-Smart-Contracts/`

_(The Decentralized Ethereum Core)_

This directory runs the local blockchain server (`npx hardhat node`) and compiles and hosts the Smart Contracts. **None of the e-commerce UI is in here.** This purely acts as the digital bank.

- **`blockchain/`**: The main Hardhat workspace.
  - **`contracts/`**:
    - `EcommerceStore.sol`: The Solidity file. This is the actual immutable ledger that receives and logs the Ethereum payments. Think of it as a transparent, automated escrow service that never crashes.
  - **`scripts/`**:
    - `deploy_store.js`: A JavaScript runner that takes your compiled Solidity code and pushes it permanently onto your Localhost 8545 Ethereum node.
  - **`hardhat.config.js`**: Stores the rules and configuration settings for compiling your Smart Contract. It sets the local chain ID to 31337.
  - **`package.json`**: Tracks the JavaScript Web3 toolkits you need installed (Ethers.js and Hardhat).

---

## 🛒 2. `Online Shopping Portal project-V2.0/`

_(The Traditional Web2 Frontend & Backend)_

This directory holds the entire traditional PHP and MySQL application for your users to actually browse, shop, and manage their orders.

- **`shopping/`**: The main Document Root directory to be served on `localhost:8080`.
  - **`index.php`**: The homepage of the e-commerce store pulling product data from the database.
  - **`blockchain-payment.php`**: The most important Web3 bridge file! This file connects the traditional frontend directly to MetaMask using `ethers.js` via the browser window. It takes the USD-equivalent subtotal of the cart, executes an Ethereum smart contract transaction from the user's wallet, and waits for a success receipt.
  - **`update-blockchain-status.php`**: After `blockchain-payment.php` gets a successful receipt from Ethereum, it sends an invisible AJAX POST request to this PHP script. This script then permanently registers the immutable `transactionHash` against that specific Order ID into the MySQL `orders` table.

---

### 📂 Behind the Scenes Connectivity

In this hybrid structure, the `Online Shopping Portal` relies absolutely on the `Blockchain-Smart-Contracts` folder running in the background.

When a customer pays for an item, the action leaves the traditional web server (PHP) completely and bounces onto the blockchain (Node + Hardhat). Once the Ethereum node confirms the transaction block is valid, it sends a message back to the Web Server to update the visual UI UI logic.
