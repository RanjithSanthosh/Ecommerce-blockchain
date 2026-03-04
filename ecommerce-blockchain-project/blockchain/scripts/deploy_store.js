const hre = require("hardhat");

async function main() {
  console.log("Deploying EcommerceStore...");

  const EcommerceStore = await hre.ethers.getContractFactory("EcommerceStore");
  const store = await EcommerceStore.deploy();

  await store.waitForDeployment();

  const address = await store.getAddress();
  console.log("EcommerceStore deployed to:", address);
}

main().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
