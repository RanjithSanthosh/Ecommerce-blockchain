/**
 * Blockchain Order Verification Module
 * 
 * Handles UI interactions and API calls for blockchain verification
 * Features:
 * - Verify order on blockchain
 * - Display verification status
 * - Show verification history
 * - Handle verification errors
 */

class BlockchainVerification {
    constructor(orderId, email) {
        this.orderId = orderId;
        this.email = email;
        this.apiEndpoint = 'blockchain/verify-order-api.php';
        this.statusMap = {
            'pending': { color: '#FFC107', icon: '⏳', label: 'Awaiting Verification' },
            'verified': { color: '#28A745', icon: '✓', label: 'Verified' },
            'failed': { color: '#DC3545', icon: '✗', label: 'Failed' },
            'cancelled': { color: '#6C757D', icon: '⊗', label: 'Cancelled' }
        };
        
        this.init();
    }
    
    /**
     * Initialize module
     */
    init() {
        this.loadStatus();
        this.setupEventListeners();
    }
    
    /**
     * Setup event listeners
     */
    setupEventListeners() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-verify-blockchain')) {
                e.preventDefault();
                this.verifyOrder();
            }
            if (e.target.closest('.btn-view-history')) {
                e.preventDefault();
                this.viewHistory();
            }
            if (e.target.closest('.btn-queue-blockchain')) {
                e.preventDefault();
                this.queueForBlockchain();
            }
            if (e.target.closest('.btn-refresh-status')) {
                e.preventDefault();
                this.loadStatus();
            }
        });
    }
    
    /**
     * Load current verification status
     */
    loadStatus() {
        this.apiCall('get_status', {}, (response) => {
            this.displayStatus(response.data);
        }, (error) => {
            this.showError('Failed to load status: ' + error);
        });
    }
    
    /**
     * Display verification status on page
     */
    displayStatus(statusData) {
        const container = document.getElementById('blockchain-verification-widget');
        if (!container) return;
        
        const status = statusData.trust_status;
        const statusInfo = this.statusMap[status] || this.statusMap['pending'];
        
        let html = `
            <div class="blockchain-status-widget" style="border-left: 4px solid ${statusInfo.color}">
                <div class="status-header">
                    <span class="status-icon" style="color: ${statusInfo.color}">${statusInfo.icon}</span>
                    <h4 class="status-title">Blockchain Verification</h4>
                    <button class="btn btn-sm btn-link btn-refresh-status" title="Refresh status">
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
                
                <div class="status-body">
                    <div class="status-info">
                        <label>Status:</label>
                        <span class="badge" style="background-color: ${statusInfo.color}">
                            ${statusInfo.label}
                        </span>
                    </div>
                    
                    ${statusData.blockchain_txid && statusData.blockchain_txid !== 'Not recorded' ? `
                        <div class="status-info">
                            <label>Transaction ID:</label>
                            <code>${statusData.blockchain_txid.substring(0, 20)}...</code>
                        </div>
                    ` : ''}
                    
                    ${statusData.blockchain_timestamp && statusData.blockchain_timestamp !== 'N/A' ? `
                        <div class="status-info">
                            <label>Verified At:</label>
                            <span>${new Date(statusData.blockchain_timestamp).toLocaleString()}</span>
                        </div>
                    ` : ''}
                    
                    <div class="status-info">
                        <label>Attempts:</label>
                        <span>${statusData.verification_attempts}</span>
                    </div>
                </div>
                
                <div class="status-actions">
                    ${statusData.can_verify ? `
                        <button class="btn btn-sm btn-primary btn-verify-blockchain">
                            <i class="fa fa-shield"></i> Verify on Blockchain
                        </button>
                    ` : ''}
                    
                    ${statusData.trust_status === 'verified' ? `
                        <button class="btn btn-sm btn-success btn-view-history">
                            <i class="fa fa-history"></i> View History
                        </button>
                    ` : ''}
                    
                    ${statusData.trust_status === 'pending' ? `
                        <button class="btn btn-sm btn-info btn-queue-blockchain">
                            <i class="fa fa-clock-o"></i> Queue for Processing
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        this.setupEventListeners();
    }
    
    /**
     * Verify order on blockchain
     */
    verifyOrder() {
        const btn = event.target.closest('.btn-verify-blockchain');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Verifying...';
        
        this.apiCall('verify', {}, (response) => {
            if (response.success) {
                this.showSuccess('Order verified successfully on blockchain!');
                this.displayVerificationResult(response.data);
                this.loadStatus();
            } else {
                this.showError('Verification failed: ' + response.message);
            }
            btn.disabled = false;
            btn.innerHTML = originalText;
        }, (error) => {
            this.showError('Verification error: ' + error);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
    
    /**
     * Display verification result
     */
    displayVerificationResult(data) {
        const modal = this.createModal('Blockchain Verification Result');
        
        let html = `
            <div class="verification-result">
                <div class="alert alert-success">
                    <h4><i class="fa fa-check-circle"></i> ${data.message}</h4>
                </div>
                
                <table class="table table-sm">
                    <tr>
                        <td><strong>Order ID:</strong></td>
                        <td>${data.order_id}</td>
                    </tr>
                    <tr>
                        <td><strong>Transaction Hash:</strong></td>
                        <td><code>${data.blockchain_txid}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Order Hash:</strong></td>
                        <td><code>${data.hash}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Verified At:</strong></td>
                        <td>${new Date().toLocaleString()}</td>
                    </tr>
                </table>
                
                ${data.verification_data ? `
                    <div class="verification-details">
                        <h5>Blockchain Details</h5>
                        <pre>${JSON.stringify(data.verification_data, null, 2)}</pre>
                    </div>
                ` : ''}
            </div>
        `;
        
        modal.querySelector('.modal-body').innerHTML = html;
        this.showModal(modal);
    }
    
    /**
     * View verification history
     */
    viewHistory() {
        this.apiCall('get_history', {}, (response) => {
            const modal = this.createModal('Verification History');
            
            if (response.data.history.length === 0) {
                modal.querySelector('.modal-body').innerHTML = 
                    '<p class="text-muted">No verification history yet.</p>';
            } else {
                let html = `
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>From Status</th>
                                <th>To Status</th>
                                <th>Transaction Hash</th>
                                <th>Verified By</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                response.data.history.forEach(event => {
                    html += `
                        <tr>
                            <td>${new Date(event.timestamp).toLocaleString()}</td>
                            <td><span class="badge badge-secondary">${event.from_status || 'Initial'}</span></td>
                            <td><span class="badge badge-info">${event.to_status}</span></td>
                            <td><code>${event.transaction_hash ? event.transaction_hash.substring(0, 16) + '...' : 'N/A'}</code></td>
                            <td>${event.verified_by}</td>
                        </tr>
                    `;
                });
                
                html += `
                        </tbody>
                    </table>
                `;
                
                modal.querySelector('.modal-body').innerHTML = html;
            }
            
            this.showModal(modal);
        }, (error) => {
            this.showError('Failed to load history: ' + error);
        });
    }
    
    /**
     * Queue order for blockchain processing
     */
    queueForBlockchain() {
        const btn = event.target.closest('.btn-queue-blockchain');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Queuing...';
        
        this.apiCall('queue_for_blockchain', {}, (response) => {
            if (response.success) {
                this.showSuccess(response.data.message);
                setTimeout(() => this.loadStatus(), 2000);
            } else {
                this.showError('Queue error: ' + response.message);
            }
            btn.disabled = false;
            btn.innerHTML = originalText;
        }, (error) => {
            this.showError('Queue error: ' + error);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
    
    /**
     * Make API call
     */
    apiCall(action, extraParams, onSuccess, onError) {
        const params = new URLSearchParams({
            action: action,
            order_id: this.orderId,
            email: this.email,
            ...extraParams
        });
        
        fetch(`${this.apiEndpoint}?${params.toString()}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    onSuccess(data);
                } else {
                    onError(data.message);
                }
            })
            .catch(error => {
                onError(error.message);
            });
    }
    
    /**
     * Show success notification
     */
    showSuccess(message) {
        this.showNotification(message, 'success');
    }
    
    /**
     * Show error notification
     */
    showError(message) {
        this.showNotification(message, 'danger');
    }
    
    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        const alertClass = `alert-${type}`;
        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        const container = document.querySelector('.body-content') || document.body;
        container.insertBefore(alert, container.firstChild);
        
        setTimeout(() => alert.remove(), 5000);
    }
    
    /**
     * Create modal dialog
     */
    createModal(title) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        `;
        return modal;
    }
    
    /**
     * Show modal
     */
    showModal(modal) {
        document.body.appendChild(modal);
        $(modal).modal('show');
        modal.addEventListener('hidden.bs.modal', () => modal.remove());
    }
}

/**
 * Initialize when document is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Look for blockchain widget initialization
    const orderIdElement = document.querySelector('[data-order-id]');
    const emailElement = document.querySelector('[data-customer-email]');
    
    if (orderIdElement && emailElement) {
        const orderId = orderIdElement.getAttribute('data-order-id');
        const email = emailElement.getAttribute('data-customer-email');
        
        window.blockchainVerification = new BlockchainVerification(orderId, email);
    }
});
