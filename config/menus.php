<?php
return [
    'available' => [
        'dashboard' => [
            'name' => 'Dashboard',
            'except_permissions' => ['create', 'update', 'show', 'trash', 'delete',], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']
                'total_cash' => 'User can see total cash',
                'bank_balance' => 'User can see bank balance',
                'current_stock' => 'User can see current stock',
            ],
        ],

        'bank' => [
            'name' => 'Bank',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],


        'bank_account' => [
            'name' => 'Bank Account',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'bank_transaction' => [
            'name' => 'Bank Transaction',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'user' => [
            'name' => 'User',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'expense' => [
            'name' => 'Expense',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],

        ],

        'cash' => [
            'name' => 'Cash',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'category' => [
            'name' => 'Category',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'brand' => [
            'name' => 'Brand',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],


        'showroom' => [
            'name' => 'Showroom',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'product' => [
            'name' => 'Product',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [
                'purchase_price' => 'Who can see purchase price',
            ],
        ],

        'product_transfer' => [
            'name' => 'Product Transfer',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'current_stock' => [
            'name' => 'Current Stock',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [
                'purchase_price' => 'Who can see purchase price',
            ],
        ],

        'damage_stock' => [
            'name' => 'Damage Stock',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'damage_return' => [
            'name' => 'Damage Return',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [// Example ['action' => 'Action description']

            ],
        ],

        'unit' => [
            'name' => 'Unit',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'loan' => [
            'name' => 'Loan',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'todo' => [
            'name' => 'Todo',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'production' => [
            'name' => 'Production',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'permission' => [
            'name' => 'Permission',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'role' => [
            'name' => 'Role',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'purchase' => [
            'name' => 'purchase',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'purchase_return' => [
            'name' => 'Purchase Return',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'sale' => [
            'name' => 'Sale',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'sale_return' => [
            'name' => 'Sale Return',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'customer' => [
            'name' => 'Customer',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'mokam' => [
            'name' => 'Mokam',
            'except_permissions' => ['show'], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'supplier' => [
            'name' => 'Supplier',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'due_management' => [
            'name' => 'Due Management',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'ledger_account' => [
            'name' => 'Ledger Account',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'transaction' => [
            'name' => 'Transaction',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'advanced_salary' => [
            'name' => 'Advanced Salary',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'salary' => [
            'name' => 'Salary',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'cash_book' => [
            'name' => 'Cash Book',
            'except_permissions' => ['create', 'update', 'show', 'trash', 'delete'], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'barcode' => [
            'name' => 'Barcode',
            'except_permissions' => ['create', 'update', 'show', 'trash', 'delete'], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']

            ],
        ],

        'ledger' => [
            'name' => 'Ledger',
            'except_permissions' => ['create', 'update', 'show', 'trash', 'delete', 'view'], // Example: ['store', 'update']
            'additional_permissions' => [
                'supplier_ledger' => 'User can see supplier ledger',
                'customer_ledger' => 'User can see customer ledger',
            ],
        ],

        'income_sector' => [
            'name' => 'Income Sector',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']
            ],
        ],

        'income_record' => [
            'name' => 'Income Record',
            'except_permissions' => [], // Example: ['store', 'update']
            'additional_permissions' => [  // Example ['action' => 'Action description']
            ],
        ],

        'sms' => [
            'name' => 'SMS',
            'except_permissions' => ['create', 'update', 'show', 'trash', 'delete'], // Example: ['store', 'update']
            'additional_permissions' => [
                'group_sms' => 'user can see group sms',
                'custom_sms' => 'user can see custom sms',
            ],
        ],

        'report' => [
            'name' => 'Report',
            'except_permissions' => ['view', 'create', 'update', 'show', 'trash', 'delete'], // Example: ['store', 'update']
            'additional_permissions' => [
                'income_report' => 'User can see income report',
                'expense_report' => 'User can see expense report',
                'purchase_report' => 'User can see purchase report',
                'sale_report' => 'User can see sale report',
                'production_report' => 'User can see production report',
                'product_report' => 'User can see product report',
                'profit_loss_report' => 'User can see profit loss report',
                'overall_report' => 'User can see overall report',
                'stock_report' => 'User can see stock report',
            ],
        ],

    ]
];
