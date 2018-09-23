<?php

return [

    [
        'name' => __('Items'),
        'description' => __('Items'),
        'permissions' => [
            'report'=>['system.item.report','system.item.report'],
            'view-items'=>['system.item.index','system.item.show'],
            'create-item'=>['system.item.create','system.item.store'],
            'delete-one-item'  =>['system.item.destroy'],
            'update-item'      =>['system.item.edit','system.item.update'],
        ]
    ],
    [
        'name' => __('Items categories'),
        'description' => __('Items categories'),
        'permissions' => [
            'view-item-categories'=>['system.category.index','system.category.show'],
            'create-item-category'=>['system.category.create','system.category.store'],
            'delete-one-item-category'  =>['system.category.destroy'],
            'update-item-category'      =>['system.category.edit','system.category.update'],
        ]
    ],


    [
        'name' => __('Supplier categories'),
        'description' => __('Supplier categories'),
        'permissions' => [
            'view-supplier-categories'=>['system.supplier-category.index','system.supplier-category.show'],
            'create-supplier-category'=>['system.supplier-category.create','system.supplier-category.store'],
            'delete-one-supplier-category'  =>['system.supplier-category.destroy'],
            'update-supplier-category'      =>['system.supplier-category.edit','system.supplier-category.update'],
        ]
    ],
    [
        'name' => __('Suppliers'),
        'description' => __('Suppliers'),
        'permissions' => [
            'view-supplier'         =>['system.supplier.index','system.supplier.show'],
            'create-supplier'       =>['system.supplier.create','system.supplier.store'],
            'delete-one-supplier'   =>['system.supplier.destroy'],
            'update-supplier'       =>['system.supplier.edit','system.supplier.update'],
            'supplier-report'       =>['system.supplier-report']
        ]
    ],
    [
        'name' => __('Vacation Type'),
        'description' => __('Vacation Type'),
        'permissions' => [
            'view-vacation-type'         =>['system.type.index','system.type.show'],
            'create-vacation-type'       =>['system.type.create','system.type.store'],
            'delete-one-vacation-type'   =>['system.type.destroy'],
            'update-vacation-type'       =>['system.type.edit','system.type.update'],
        ]
    ],
    [
        'name' => __('Vacations'),
        'description' => __('Vacations'),
        'permissions' => [
            'view-vacations'         =>['system.vacation.index','system.vacation.show'],
            'create-vacation'       =>['system.vacation.create','system.vacation.store'],
            'delete-one-vacation'   =>['system.vacation.destroy'],
            'update-vacation'       =>['system.vacation.edit','system.vacation.update'],
        ]
    ],
    [
        'name' => __('Client Types'),
        'description' => __('Client Types'),
        'permissions' => [
            'view-client-types'        =>['system.types.index','system.types.show'],
            'create-client-type'       =>['system.types.create','system.types.store'],
            'delete-one-client-type'   =>['system.types.destroy'],
            'update-client-type'       =>['system.types.edit','system.types.update'],
        ]
    ],

    [
        'name' => __('Clients'),
        'description' => __('Clients'),
        'permissions' => [
            'view-clients'         =>['system.client.index','system.client.show'],
            'create-client'       =>['system.client.create','system.client.store'],
            'delete-one-client'   =>['system.client.destroy'],
            'client-report'       =>['system.client-report'],
            'update-client'       =>['system.client.edit','system.client.update'],
        ]
    ],
    [
        'name' => __('Complains'),
        'description' => __('Complains'),
        'permissions' => [
            'view-complains'         =>['system.complain.index','system.complain.show'],
            'create-complain'       =>['system.complain.create','system.complain.store'],
            'delete-one-complain'   =>['system.complain.destroy'],
            'update-complain'       =>['system.complain.edit','system.complain.update'],
        ]
    ],
    [
        'name' => __('Quotations'),
        'description' => __('Quotations'),
        'permissions' => [
            'view-quotations'         =>['system.quotations.index','system.quotations.show'],
            'create-quotations'       =>['system.quotations.create','system.quotations.store'],
            'delete-one-quotations'   =>['system.quotations.destroy'],
            'update-quotations'       =>['system.quotations.edit','system.quotations.update'],
        ]
    ],
    [
        'name' => __('Department'),
        'description' => __('Department'),
        'permissions' => [
            'view-department'         =>['system.department.index','system.department.show'],
            'create-department'       =>['system.department.create','system.department.store'],
            'delete-one-department'   =>['system.department.destroy'],
            'update-department'       =>['system.department.edit','system.department.update'],
        ]
    ],
    [
        'name' => __('Contract'),
        'description' => __('Contract'),
        'permissions' => [
            'view-contract'         =>['system.contract.index','system.contract.show'],
            'create-contract'       =>['system.contract.create','system.contract.store'],
            'delete-one-contract'   =>['system.contract.destroy'],
            'update-contract'       =>['system.contract.edit','system.contract.update'],
        ]
    ],
    [
        'name' => __('Project'),
        'description' => __('Project'),
        'permissions' => [
            'view-project'         =>['system.project.index','system.project.show'],
            'create-project'       =>['system.project.create','system.project.store'],
            'delete-one-project'   =>['system.project.destroy'],
            'update-project'       =>['system.project.edit','system.project.update'],
        ]
    ],
    [
        'name' => __('Calls'),
        'description' => __('Calls'),
        'permissions' => [
            'view-Calls'         =>['system.call.index','system.call.show'],
            'create-Call'       =>['system.call.create','system.call.store'],
            'delete-one-Call'   =>['system.call.destroy'],
            'update-Call'       =>['system.call.edit','system.call.update'],
        ]
    ],
    [
        'name' => __('Brand'),
        'description' => __('Brand'),
        'permissions' => [
            'view-Brands'         =>['system.brand.index','system.brand.show'],
            'create-Brand'       =>['system.brand.create','system.brand.store'],
            'delete-one-Brand'   =>['system.brand.destroy'],
            'update-Brand'       =>['system.brand.edit','system.brand.update'],
        ]
    ],
    [
        'name' => __('Bus'),
        'description' => __('Bus'),
        'permissions' => [
            'view-Bus'         =>['system.bus.index','system.bus.show'],
            'create-Bus'       =>['system.bus.create','system.bus.store'],
            'delete-one-Bus'   =>['system.bus.destroy'],
            'update-Bus'       =>['system.bus.edit','system.bus.update'],
        ]
    ],
    [
        'name' => __('Maintenance'),
        'description' => __('Maintenance'),
        'permissions' => [
            'view-Maintenance'         =>['system.maintenance.index','system.maintenance.show'],
            'create-Maintenance'       =>['system.maintenance.create','system.maintenance.store'],
            'delete-one-Maintenance'   =>['system.maintenance.destroy'],
            'update-Maintenance'       =>['system.maintenance.edit','system.maintenance.update'],
        ]
    ],
    [
        'name' => __('Bus Tracking'),
        'description' => __('Bus Tracking'),
        'permissions' => [
            'view-Bus-Tracking'         =>['system.tracking.index','system.tracking.show'],
            'create-Bus-Tracking'       =>['system.tracking.create','system.tracking.store'],
            'delete-one-Bus-Tracking'   =>['system.tracking.destroy'],
            'update-Bus-Tracking'       =>['system.tracking.edit','system.tracking.update'],
        ]
    ],
    [
        'name' => __('Attendance'),
        'description' => __('Attendance'),
        'permissions' => [
            'view-Attendance'         =>['system.attendance.index','system.attendance.show'],
            'create-Attendance'       =>['system.attendance.create','system.attendance.store'],
            'delete-one-Attendance'   =>['system.attendance.destroy'],
            'update-Attendance'       =>['system.attendance.edit','system.attendance.update'],
        ]
    ],
    [
        'name' => __('Attendance Groups'),
        'description' => __('Attendance Groups'),
        'permissions' => [
            'view-Attendance-Group'         =>['system.attendance-group-index'],
            'create-Attendance'       =>['system.attendance-group','system.attendance-group-store'],
            'update-Attendance'       =>['system.attendance-group-edit','system.attendance-group-update'],
        ]
    ],

    [
        'name' => __('Supplier Orders'),
        'description' => __('Supplier Orders'),
        'permissions' => [
            'view-supplier-orders'         =>['system.order.index','system.order.show'],
            'create-supplier-orders'       =>['system.order.create','system.order.store'],
            'delete-one-supplier-orders'   =>['system.order.destroy'],
            'update--supplier-orders'       =>['system.order.edit','system.order.update'],
        ]
    ],




    [
        'name' => __('Client Orders'),
        'description' => __('Client Orders'),
        'permissions' => [
            'view-client-orders'         =>['system.client-orders.index','system.client-orders.show'],
            'create-client-orders'       =>['system.client-orders.create','system.client-orders.store'],
            'delete-one-client-orders'   =>['system.client-orders.destroy'],
            'update--client-orders'       =>['system.client-orders.edit','system.client-orders.update'],
 ]
            ],

    [
        'name' => __('Expense Causes'),
        'description' => __('Expense Causes'),
        'permissions' => [
            'view-expense-causes'         =>['system.expense.index','system.expense.show'],
            'create-expense-causes'       =>['system.expense.create','system.expense.store'],
            'delete-one-expense-causes'   =>['system.expense.destroy'],
            'update-expense-causes'       =>['system.expense.edit','system.expense.update'],
        ]
    ],
    [
        'name' => __('Expense'),
        'description' => __('Expense'),
        'permissions' => [
            'view-expenses'         =>['system.expenses.index','system.expenses.show'],
            'create-expense'       =>['system.expenses.create','system.expenses.store'],
            'delete-one-expense'   =>['system.expenses.destroy'],
            'update-expense'       =>['system.expenses.edit','system.expenses.update'],
        ]
    ],
    [
        'name' => __('Profit Causes'),
        'description' => __('Profit Causes'),
        'permissions' => [
            'view-profit-causes'         =>['system.profit.index','system.profit.show'],
            'create-profit-causes'       =>['system.profit.create','system.profit.store'],
            'delete-one-profit-causes'   =>['system.profit.destroy'],
            'update-profit-causes'       =>['system.profit.edit','system.profit.update'],
        ]
    ],
    [
        'name' => __('Profit'),
        'description' => __('Profit'),
        'permissions' => [
            'view-profits'         =>['system.profits.index','system.profits.show'],
            'create-profit'       =>['system.profits.create','system.profits.store'],
            'delete-one-profit'   =>['system.profits.destroy'],
            'update-profit'       =>['system.profits.edit','system.profits.update'],

        ]
    ],
    [
        'name' => __('Deduction'),
        'description' => __('Deduction'),
        'permissions' => [
            'view-deductions'         =>['system.deduction.index','system.deduction.show'],
            'create-deduction'       =>['system.deduction.create','system.deduction.store'],
            'delete-one-deduction'   =>['system.deduction.destroy'],
            'update-deduction'       =>['system.deduction.edit','system.deduction.update'],
        ]
    ],
    [
        'name' => __('Overtime'),
        'description' => __('Overtime'),
        'permissions' => [
            'view-overtime'         =>['system.overtime.index','system.overtime.show'],
            'create-overtime'       =>['system.overtime.create','system.overtime.store'],
            'delete-one-overtime'   =>['system.overtime.destroy'],
            'update-overtime'       =>['system.overtime.edit','system.overtime.update'],

        ]
    ],
    /*
    * permission-groups
    */
    [
        'name' => __('permission-groups Permissions'),
        'description' => __('permission-group Permissions Description'),
        'permissions' => [
            'view-all-permission-groups'=>['system.permission-group.index'],
            'view-one-permission-groups'=>['system.permission-group.show'],
            'delete-one-permission-groups'=>['system.permission-group.destroy'],
            'create-permission-groups'=>['system.permission-group.create','system.permission-group.store'],
            'update-permission-groups'=>['system.permission-group.edit','system.permission-group.update'],
        ]
    ],


    /*
    * users
    */
    [
        'name' => __('Users Permissions'),
        'description' => __('Users permissions Description'),
        'permissions' => [
            'view-all-users'=>['system.users.index'],
            'view-one-user'=>['system.users.show'],
            'delete-one-user'=>['system.users.destroy'],
            'create-user'=>['system.users.create','system.users.store'],
            'update-user'=>['system.users.edit','system.users.update'],
        ]
    ],


    /*
    * staff
    */
    [
        'name' => __('Staff Permissions'),
        'description' => __('Staff Permissions Description'),
        'permissions' => [
            'view-all-staff'    =>['system.staff.index'],
            'view-one-staff'    =>['system.staff.show'],
            'delete-one-staff'  =>['system.staff.destroy'],
            'create-staff'      =>['system.staff.create','system.staff.store'],
            'update-staff'      =>['system.staff.edit','system.staff.update'],
            'add-managed-staff' =>['system.staff.add-managed-staff'],
            'delete-managed-staff' =>['system.staff.delete-managed-staff'],
            'show-tree-users-data' => ['show-tree-users-data'],
        ]
    ],
    /*
    * activity-log
    */
    [
        'name' => __('System activity log Permissions'),
        'description' => __('System activity log Permissions Description'),
        'permissions' => [
            'view-activity-log'=>['system.activity-log.show'],
        ]
    ],


    /*
    * System Setting
    */
    [
        'name' => __('System Permissions'),
        'description' => __('System settings Permissions Description'),
        'permissions' => [
            'system-settings'=>['system.setting.index','system.setting.update'],
            'activity-log'=>['system.activity-log.index','system.activity-log.show']
        ]
    ],
    /*
     * Monthly Report
     */

    [
        'name' => __('Monthly Report Permissions'),
        'description' => __('Monthly Report Permissions Description'),
        'permissions' => [
            'view-all-monthly-report'=>['system.monthly-report-index'],
            'view-one-monthly-report'=>['system.monthly-report-show'],
            'delete-monthly-report'=>['system.monthly-report-delete'],
            'create-monthly-report'=>['system.attendance.monthly-report','system.attendance.monthly-report-calc'],
        ]
    ],
    [
        'name' => __('Clothes'),
        'description' => __('Clothes'),
        'permissions' => [
            'view-clothes'         =>['system.clothes.index','system.clothes.show'],
            'create-clothe'       =>['system.clothes.create','system.clothes.store'],
            'delete-one-clothe'   =>['system.clothes.destroy'],
            'update-clothe'       =>['system.clothes.edit','system.clothes.update'],

        ]
    ], [
        'name' => __('Certificates'),
        'description' => __('Certificates'),
        'permissions' => [
            'view-certificates'         =>['system.certificates.index','system.certificates.show'],
            'create-certificate'       =>['system.certificates.create','system.certificates.store'],
            'delete-one-certificate'   =>['system.certificates.destroy'],
            'update-certificate'       =>['system.certificates.edit','system.certificates.update'],

        ]
    ],

    [
        'name' => __('Visa Tracking'),
        'description' => __('Visa Tracking'),
        'permissions' => [
            'view-Visa-Tracking'         =>['system.visa-tracking.index','system.visa-tracking.show'],
            'create-Visa-Tracking'       =>['system.visa-tracking.create','system.visa-tracking.store'],
            'delete-one-Visa-Tracking'   =>['system.visa-tracking.destroy'],
            'update-Visa-Tracking'       =>['system.visa-tracking.edit','system.visa-tracking.update'],
        ]
    ],
    [
        'name' => __('Client Order Item Back'),
        'description' => __('Client Order Item Back'),
        'permissions' => [
            'view-client-order-back'         =>['system.client-order-back.index','system.client-order-back.show'],
            'create-client-order-back'       =>['system.client-order-back.create','system.client-order-back.store'],
            'delete-one-client-order-back'   =>['system.client-order-back.destroy'],
        ]
    ],
    [
        'name' => __('Supplier Order Item Back'),
        'description' => __('Supplier Order Item Back'),
        'permissions' => [
            'view-supplier-order-back'         =>['system.supplier-order-back.index','system.supplier-order-back.show'],
            'create-supplier-order-back'       =>['system.supplier-order-back.create','system.supplier-order-back.store'],
            'delete-one-supplier-order-back'   =>['system.supplier-order-back.destroy'],
        ]
    ],

];
