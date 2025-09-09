/**
 * Global Payment Terms Utility
 * Handles payment terms behavior consistently across all modules
 */

window.PaymentTermsHandler = {
    
    /**
     * Initialize payment terms behavior for a form
     * @param {Object} config Configuration object
     * @param {string} config.paymentTermsSelector - Selector for payment terms dropdown
     * @param {string} config.daysRowSelector - Selector for days row container
     * @param {string} config.daysFieldSelector - Selector for days input field
     * @param {string} config.dueDateFieldSelector - Selector for due date input field
     * @param {string} config.transactionDateSelector - Selector for transaction date field
     */
    init: function(config) {
        this.config = config;
        this.bindEvents();
        this.initializeState();
    },
    
    /**
     * Bind event handlers
     */
    bindEvents: function() {
        const self = this;
        
        // Payment terms change
        $(this.config.paymentTermsSelector).on('change', function() {
            self.handlePaymentTermsChange();
            self.calculateDueDate();
        });
        
        // Days field change (for CUSTOM type)
        $(this.config.daysFieldSelector).on('input', function() {
            self.calculateDueDate();
        });
        
        // Transaction date change
        $(this.config.transactionDateSelector).on('change', function() {
            self.calculateDueDate();
        });
    },
    
    /**
     * Initialize the state on page load
     */
    initializeState: function() {
        this.handlePaymentTermsChange();
        this.calculateDueDate();
    },
    
    /**
     * Handle payment terms change to show/hide conditional fields
     */
    handlePaymentTermsChange: function() {
        const selectedOption = $(this.config.paymentTermsSelector + ' option:selected');
        const type = selectedOption.data('type');
        const days = parseInt(selectedOption.data('days')) || 0;
        
        const daysRow = $(this.config.daysRowSelector);
        const daysField = $(this.config.daysFieldSelector);
        const dueDateField = $(this.config.dueDateFieldSelector);
        
        if (type === 'NET' || type === 'CUSTOM') {
            // Show the row for NET and CUSTOM types
            daysRow.show();
            
            if (type === 'NET') {
                // For NET type: auto-fill days and disable field
                daysField.val(days).prop('disabled', true);
                dueDateField.prop('disabled', true);
            } else if (type === 'CUSTOM') {
                // For CUSTOM type: enable days field only, due date is always auto-calculated
                // Preserve existing value if in edit mode, otherwise use default
                const currentValue = daysField.val();
                daysField.val(currentValue || days || '').prop('disabled', false);
                dueDateField.prop('disabled', true);
            }
        } else {
            // Hide the row for other types (COD, IMMEDIATE, etc.)
            daysRow.hide();
            daysField.val('').prop('disabled', true);
            dueDateField.prop('disabled', true);
        }
    },
    
    /**
     * Calculate due date based on payment terms
     */
    calculateDueDate: function() {
        const transactionDate = $(this.config.transactionDateSelector).val();
        const selectedOption = $(this.config.paymentTermsSelector + ' option:selected');
        const type = selectedOption.data('type');
        const dueDateField = $(this.config.dueDateFieldSelector);
        const daysField = $(this.config.daysFieldSelector);
        
        // Auto-calculate for NET type, use manual input for CUSTOM type
        if (transactionDate && type) {
            const date = new Date(transactionDate);
            let days = 0;
            
            if (type === 'NET') {
                days = parseInt(selectedOption.data('days')) || 0;
            } else if (type === 'CUSTOM') {
                days = parseInt(daysField.val()) || 0;
            }
            
            if (type === 'NET' || (type === 'CUSTOM' && days > 0)) {
                date.setDate(date.getDate() + days);
                const dueDate = date.toISOString().split('T')[0];
                dueDateField.val(dueDate);
            }
        }
    }
};