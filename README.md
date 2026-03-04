# 🛒 Decentralized E-commerce Shopping Portal

Welcome to the **Decentralized E-commerce Shopping Portal**, a Hybrid full-stack project combining traditional Web2 server architecture with Web3 Ethereum Blockchain for secure, decentralized payment validation.

This project enhances a legacy PHP/MySQL Online Shopping Portal by removing traditional banking payment intermediaries (like Stripe or PayPal) and replacing them with an immutable, transparent, and direct peer-to-peer cryptocurrency payment gateway using Ethereum Smart Contracts.

### 🌟 Project Sections Overview

To make navigating this complex project easier, all documentation has been split into independent reference files:

1. **[SETUP_AND_RUN.md](./SETUP_AND_RUN.md)**
   - A step-by-step guide on how to install dependencies and run both the PHP server and the Local Blockchain Node.
2. **[BLOCKCHAIN_INTEGRATION.md](./BLOCKCHAIN_INTEGRATION.md)**
   - Explains exactly _how_ the blockchain works in this app, what libraries we use, and the underlying logic of a decentralized checkout.
3. **[FOLDER_STRUCTURE.md](./FOLDER_STRUCTURE.md)**
   - A detailed breakdown of every significant folder and file in this repository and what its exact purpose is.

### 🚀 Quick Highlights

- **Smart Contracts**: Written in Solidity and deployed locally on Hardhat.
- **Frontend Wallet**: Real-time MetaMask integration via pure JavaScript (`ethers.js`).
- **Backend Anchor**: The traditional PHP backend verifies receipt and anchors the immutable `Transaction Hash` into the MySQL database as definitive Proof of Purchase.

_Ready to start? Open `SETUP_AND_RUN.md` to run the project!_
