// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

/**
 * @title EcommerceStore
 * @dev Secure payment gateway and order registry for the Online Shopping Portal.
 */
contract EcommerceStore {
    address public owner;
    
    struct Order {
        uint256 orderId;
        address buyer;
        uint256 amount;
        bytes32 orderHash;
        uint256 timestamp;
        bool isPaid;
    }
    
    mapping(uint256 => Order) public orders;
    uint256[] public orderIds;
    
    event OrderPaid(
        uint256 indexed orderId,
        address indexed buyer,
        uint256 amount,
        bytes32 orderHash,
        uint256 timestamp
    );
    
    event Withdrawal(address indexed to, uint256 amount);
    
    modifier onlyOwner() {
        require(msg.sender == owner, "Only owner can call this");
        _;
    }
    
    constructor() {
        owner = msg.sender;
    }
    
    function payForOrder(uint256 _orderId, bytes32 _orderHash) external payable {
        require(msg.value > 0, "Payment amount must be greater than 0");
        require(!orders[_orderId].isPaid, "Order already paid");
        
        orders[_orderId] = Order({
            orderId: _orderId,
            buyer: msg.sender,
            amount: msg.value,
            orderHash: _orderHash,
            timestamp: block.timestamp,
            isPaid: true
        });
        
        orderIds.push(_orderId);
        
        emit OrderPaid(_orderId, msg.sender, msg.value, _orderHash, block.timestamp);
    }
    
    function verifyPayment(uint256 _orderId) external view returns (bool, address, uint256, bytes32) {
        Order memory o = orders[_orderId];
        return (o.isPaid, o.buyer, o.amount, o.orderHash);
    }
    
    function withdraw() external onlyOwner {
        uint256 balance = address(this).balance;
        require(balance > 0, "No funds to withdraw");
        (bool success, ) = payable(owner).call{value: balance}("");
        require(success, "Withdrawal failed");
        emit Withdrawal(owner, balance);
    }
    
    function getOrderCount() external view returns (uint256) {
        return orderIds.length;
    }

    receive() external payable {}
    fallback() external payable {}
}
