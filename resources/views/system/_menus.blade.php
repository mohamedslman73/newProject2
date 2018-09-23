@php


    $menu['Dashboard'] = [
           'url'=> route('system.dashboard'),
           'icon'=>'ft-home',
           'text'=>__('Dashboard'),
    ];



$menu['HR']= [

 'text'=>__('HR'),
  'icon'=>'fa fa-user',
           'sub'=>[

 'Visa Tracking'=>[
           'permission'=> [
                'system.visa-tracking.index',
                'system.visa-tracking.create',
                'system.visa-tracking.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-cc-visa',
           'text'=>__('Visa Tracking'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.visa-tracking.index',
                   'url'=> route('system.visa-tracking.index'),
                   'text'=> __('View Visa Tracking'),
               ],

               'Create'=> [
                   'permission'=> 'system.visa-tracking.create',
                   'url'=> route('system.visa-tracking.create'),
                   'text'=> __('Create Visa Tracking'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.visa-tracking.index',
                   'url'=> route('system.visa-tracking.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Visa Tracking')
               ],
            ]
           ],

          'Certificates'=> [
           'permission'=> [
                'system.certificates.index',
                'system.certificates.create',
                'system.certificates.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-certificate',
           'text'=>__('Certificates'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.certificates.index',
                   'url'=> route('system.certificates.index'),
                   'text'=> __('View Certificates'),
               ],

               'Create'=> [
                   'permission'=> 'system.certificates.create',
                   'url'=> route('system.certificates.create'),
                   'text'=> __('Create Certificate'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.certificates.index',
                   'url'=> route('system.certificates.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Certificate')
               ],
            ]
           ],


          'Clothes'=>  [
           'permission'=> [
                'system.clothes.index',
                'system.clothes.create',
                'system.clothes.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-universal-access',
           'text'=>__('clothes'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.clothes.index',
                   'url'=> route('system.clothes.index'),
                   'text'=> __('View Clothes'),
               ],

               'Create'=> [
                   'permission'=> 'system.clothes.create',
                   'url'=> route('system.clothes.create'),
                   'text'=> __('Create Clothes'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.clothes.index',
                   'url'=> route('system.clothes.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Clothes')
               ],
            ]
           ],


           'Vacations Types' => [
           'permission'=> [
                'system.type.index',
                'system.type.create',
                'system.type.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-industry',
           'text'=>__('Vacation Types'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.type.index',
                   'url'=> route('system.type.index'),
                   'text'=> __('View vacation Types'),
               ],

               'Create'=> [
                   'permission'=> 'system.type.create',
                   'url'=> route('system.type.create'),
                   'text'=> __('Create Vacation Types'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.type.index',
                   'url'=> route('system.type.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Vacation Types')
               ],



           ],
  ],


  'Vacations'=>[
             'permission'=> [
                'system.vacation.index',
                'system.vacation.create',
                'system.vacation.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-plane',
           'text'=>__('Vacations'),
           'sub'=>[
           'View'=> [
                   'permission'=> 'system.vacation.index',
                   'url'=> route('system.vacation.index'),
                   'text'=> __('View Vacations'),
               ],

               'Create'=> [
                   'permission'=> 'system.vacation.create',
                   'url'=> route('system.vacation.create'),
                   'text'=> __('Create Vacations'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.vacation.index',
                   'url'=> route('system.vacation.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Vacations')
               ],

           ]
            ],

        'Deduction'=> [
           'permission'=> [
                'system.deduction.index',
                'system.deduction.create',
                'system.deduction.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-dollar',
           'text'=>__('Deduction'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.deduction.index',
                   'url'=> route('system.deduction.index'),
                   'text'=> __('View Deduction'),
               ],

               'Create'=> [
                   'permission'=> 'system.deduction.create',
                   'url'=> route('system.deduction.create'),
                   'text'=> __('Create Deduction'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.deduction.index',
                   'url'=> route('system.deduction.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Deduction')
               ],
            ]
           ],


           'Overtime'=>[

           'permission'=> [
                'system.overtime.index',
                'system.overtime.create',
                'system.overtime.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-dollar',
           'text'=>__('Overtime'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.overtime.index',
                   'url'=> route('system.overtime.index'),
                   'text'=> __('View Overtime'),
               ],

               'Create'=> [
                   'permission'=> 'system.overtime.create',
                   'url'=> route('system.overtime.create'),
                   'text'=> __('Create Overtime'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.overtime.index',
                   'url'=> route('system.overtime.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Overtime')
               ],
            ]

           ],

           'Attendance' =>[

           'permission'=> [
                'system.attendance.index',
                'system.attendance.create',
                'system.attendance.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-calendar-check-o',
           'text'=>__('Attendance'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.attendance.index',
                   'url'=> route('system.attendance.index'),
                   'text'=> __('View Attendance'),
               ],

               'Create'=> [
                   'permission'=> 'system.attendance.create',
                   'url'=> route('system.attendance.create'),
                   'text'=> __('Create Attendance'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.attendance.index',
                   'url'=> route('system.attendance.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Attendance')
               ],


           ],
         'Attendance'=>  [
           'permission'=> [
                'system.attendance-group-index',
                'system.attendance-group',
                    'system.attendance-group-edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-calendar',
           'text'=>__('Attendance Groups'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.attendance-group-index',
                   'url'=> route('system.attendance-group-index'),
                   'text'=> __('View Attendance Groups'),
               ],

               'Create'=> [
                   'permission'=> 'system.attendance-group',
                   'url'=> route('system.attendance-group'),
                   'text'=> __('Create Attendance Group'),
               ],

                ]
           ] ],

           'Monthly Reports'=>

[
             'permission'=> [
                 'system.monthly-report-index',
                'system.attendance.monthly-report',
                ],
           'class'=>'',
           'icon'=>'fa fa-flag',
           'text'=>__('Monthly Report'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.monthly-report-index',
                   'url'=> route('system.monthly-report-index'),
                   'text'=> __('View Monthly Reports'),
               ],

               'Create'=> [
                   'permission'=> 'system.attendance.monthly-report',
                   'url'=> route('system.attendance.monthly-report'),
                   'text'=> __('Create Monthly Report'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.monthly-report-index',
                   'url'=> route('system.monthly-report-index',['withTrashed'=>1]),
                   'text'=> __('Trashed Monthly Reports')
               ],
            ]

           ],




               'Users'=> [
                   'permission'=> [
                       'system.staff.index',
                       'system.staff.create'
                   ],
                   'icon'=>'fa fa-user',
                   'text'=> __('Users'),
                   'sub'=>[
                       [
                           'permission'=> 'system.staff.index',
                           'url'=> route('system.staff.index'),
                           'text'=> __('View Users')
                       ],
                       [
                           'permission'=> 'system.staff.create',
                           'url'=> route('system.staff.create'),
                           'text'=> __('Create User')
                       ],
                       [
                           'permission'=> 'system.staff.create',
                           'url'=> route('system.staff.visa-report'),
                           'text'=> __('Visa Report')
                       ],
                       [
                           'aClass'=> 'color-red',
                           'permission'=> 'system.staff.index',
                           'url'=> route('system.staff.index',['withTrashed'=>1]),
                           'text'=> __('Trashed Users')
                       ],

                   ]
               ],



               'Permission'=> [
                   'permission'=> [
                       'system.permission-group.index',
                       'system.permission-group.create',
                       'system.permission-group.edit'
                   ],
                   'icon'=>'fa fa-universal-access',
                   'text'=> __('Permissions'),
                   'sub'=>[
                       [
                           'permission'=> 'system.permission-group.index',
                           'url'=> route('system.permission-group.index'),
                           'text'=> __('View Permissions')
                       ],
                       [
                           'permission'=> 'system.permission-group.create',
                           'url'=> route('system.permission-group.create'),
                           'text'=> __('Create Permission')
                       ],
                       [
                           'aClass'=> 'color-red',
                           'permission'=> 'system.permission-group.index',
                           'url'=> route('system.permission-group.index',['withTrashed'=>1]),
                           'text'=> __('Trashed Permissions')
                       ],

                   ]
               ],

]


];





 $menu['Suppliers '] = [

           'permission'=> [
                'system.supplier.index',
                'system.supplier.create',
                'system.supplier.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-user',
           'text'=>__('Suppliers'),
           'sub'=>[

           'Suppliers Categories' =>[
           'permission'=> [
                'system.supplier-category.index',
                'system.supplier-category.create',
                'system.supplier-category.edit',
                ],
           'class'=>'',
           'icon'=>' fa fa-object-group',
           'text'=>__(' Categories'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.supplier-category.index',
                   'url'=> route('system.supplier-category.index'),
                   'text'=> __('View supplier Category'),
               ],

               'Create'=> [
                   'permission'=> 'system.supplier-category.create',
                   'url'=> route('system.supplier-category.create'),
                   'text'=> __('Create Supplier Category'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.supplier-category.index',
                   'url'=> route('system.supplier-category.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Supplier Category')
               ],

           ]

           ],

            'Suppliers' =>[
           'permission'=> [
                'system.supplier.index',
                'system.supplier.create',
                'system.supplier.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-building',
           'text'=>__(' Suppliers'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.supplier.index',
                   'url'=> route('system.supplier.index'),
                   'text'=> __('View supplier'),
               ],

               'Create'=> [
                   'permission'=> 'system.supplier.create',
                   'url'=> route('system.supplier.create'),
                   'text'=> __('Create Supplier'),
               ],
               'Supplier Report'=> [
                   'permission'=> 'system.supplier-report',
                   'url'=> route('system.supplier-report'),
                   'text'=> __('Supplier Report'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.supplier.index',
                   'url'=> route('system.supplier.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Supplier')
               ],

           ]

           ],

           ' Orders' =>[
           'permission'=> [
                'system.order.index',
                'system.order.create',
                'system.order.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-shopping-cart',
           'text'=>__(' Order'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.order.index',
                   'url'=> route('system.order.index'),
                   'text'=> __('View Supplier Order'),
               ],

               'Create'=> [
                   'permission'=> 'system.order.create',
                   'url'=> route('system.order.create'),
                   'text'=> __('Create Supplier Order'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.order.index',
                   'url'=> route('system.order.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Supplier Order')
               ],

           ]

           ],
           ' Orders Back' =>[
           'permission'=> [
                'system.supplier-order-back.index',
                'system.supplier-order-back.create',
                'system.supplier-order-back.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-shopping-cart',
           'text'=>__(' Order Back'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.supplier-order-back.index',
                   'url'=> route('system.supplier-order-back.index'),
                   'text'=> __('View Supplier Order Back'),
               ],

               'Create'=> [
                   'permission'=> 'system.supplier-order-back.create',
                   'url'=> route('system.supplier-order-back.create'),
                   'text'=> __('Create Supplier Order Back'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.supplier-order-back.index',
                   'url'=> route('system.supplier-order-back.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Supplier Order Back')
               ],

           ]

           ],

        ]

       ];




 $menu['Client'] = [
           'permission'=> [
                'system.client.index',
                'system.client.create',
                'system.client.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-users',
           'text'=>__('Client'),
           'sub'=>[

           'Client Type' => [
           'permission'=> [
                'system.types.index',
                'system.types.create',
                'system.types.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-low-vision',
           'text'=>__('Client Types'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.types.index',
                   'url'=> route('system.types.index'),
                   'text'=> __('View Client Types'),
               ],

               'Create'=> [
                   'permission'=> 'system.types.create',
                   'url'=> route('system.types.create'),
                   'text'=> __('Create Client Types'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.types.index',
                   'url'=> route('system.types.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Client Types')
               ],

           ]
       ],

       'Client' => [
           'permission'=> [
               'system.client.index',
                'system.client.create',
                'system.client.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-low-vision',
           'text'=>__('Client '),
           'sub'=>[


      'View'=> [
                   'permission'=> 'system.client.index',
                   'url'=> route('system.client.index'),
                   'text'=> __('View Clients'),
               ],

               'Create'=> [
                   'permission'=> 'system.client.create',
                   'url'=> route('system.client.create'),
                   'text'=> __('Create Clients'),
               ],
                'Client Report'=> [
                   'permission'=> 'system.client-report',
                   'url'=> route('system.client-report'),
                   'text'=> __('Client Report'),
               ],
               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.client.index',
                   'url'=> route('system.client.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Clients')
               ],


         ],
         ],


           'Orders'=>[
           'permission'=> [
                'system.client-orders.index',
                'system.client-orders.create',
                'system.client-orders.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-dollar',
           'text'=>__('Orders'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.client-orders.index',
                   'url'=> route('system.client-orders.index'),
                   'text'=> __('View Orders'),
               ],

               'Create'=> [
                   'permission'=> 'system.client-orders.create',
                   'url'=> route('system.client-orders.create'),
                   'text'=> __('Create Order'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.client-orders.index',
                   'url'=> route('system.client-orders.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Orders')
               ],
            ]
           ],


        'Order Back' => [
           'permission'=> [
                'system.client-order-back.index',
                'system.client-order-back.create',
                'system.client-order-back.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-low-vision',
           'text'=>__('Order Back'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.client-order-back.index',
                   'url'=> route('system.client-order-back.index'),
                   'text'=> __('View Client Order Back'),
               ],

               'Create'=> [
                   'permission'=> 'system.client-order-back.create',
                   'url'=> route('system.client-order-back.create'),
                   'text'=> __('Create Client Order Back'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.client-order-back.index',
                   'url'=> route('system.client-order-back.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Client Order Back')
               ],

           ]
       ],


           ]
       ];



 $menu['Complains'] = [
           'permission'=> [
                'system.complaint.index',
                'system.complaint.create',
                'system.complaint.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-users',
           'text'=>__('Complains'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.complaint.index',
                   'url'=> route('system.complaint.index'),
                   'text'=> __('View Complaints'),
               ],

               'Create'=> [
                   'permission'=> 'system.complaint.create',
                   'url'=> route('system.complaint.create'),
                   'text'=> __('Create complaint'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.complaint.index',
                   'url'=> route('system.complaint.index',['withTrashed'=>1]),
                   'text'=> __('Trashed complaint')
               ],

           ]
       ];


$menu['Calls'] = [
           'permission'=> [
                'system.call.index',
                'system.call.create',
                'system.call.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-phone',
           'text'=>__('Calls'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.call.index',
                   'url'=> route('system.call.index'),
                   'text'=> __('View Calls'),
               ],

               'Create'=> [
                   'permission'=> 'system.call.create',
                   'url'=> route('system.call.create'),
                   'text'=> __('Create Call'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.call.index',
                   'url'=> route('system.call.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Calls')
               ],

           ]
       ];




$menu['Bus'] = [

 'icon'=>'fa fa-bus',
 'text'=>__('Buses'),
 'sub'=>[

'Brand'=>[

           'permission'=> [
                'system.brand.index',
                'system.brand.create',
                'system.brand.edit',
                ],
           'class'=>'',
            'icon'=>'fa fa-rss',
           'text'=>__('Brands'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.brand.index',
                   'url'=> route('system.brand.index'),
                   'text'=> __('View Brands'),
               ],

               'Create'=> [
                   'permission'=> 'system.brand.create',
                   'url'=> route('system.brand.create'),
                   'text'=> __('Create Brand'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.brand.index',
                   'url'=> route('system.brand.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Brands')
               ],

           ]

       ],

       'Bus' => [ 'permission'=> [
                'system.bus.index',
                'system.bus.create',
                'system.bus.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-bus',
           'text'=>__('Bus'),
           'sub'=>[


            'View'=> [
                   'permission'=> 'system.bus.index',
                   'url'=> route('system.bus.index'),
                   'text'=> __('View Bus'),
               ],

               'Create'=> [
                   'permission'=> 'system.bus.create',
                   'url'=> route('system.bus.create'),
                   'text'=> __('Create Bus'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.bus.index',
                   'url'=> route('system.bus.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Bus')
               ],

               ]
               ],
'Tracking'=>   [
           'permission'=> [
                'system.tracking.index',
                'system.tracking.create',
                'system.tracking.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-map-marker',
           'text'=>__('Tracking'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.tracking.index',
                   'url'=> route('system.tracking.index'),
                   'text'=> __('View Bus Tracking'),
               ],

               'Create'=> [
                   'permission'=> 'system.tracking.create',
                   'url'=> route('system.tracking.create'),
                   'text'=> __('Create Bus Tracking'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.tracking.index',
                   'url'=> route('system.tracking.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Bus Tracking')
               ],

           ]
       ],
        'Maintenance'=>    [
           'permission'=> [
                'system.maintenance.index',
                'system.maintenance.create',
                'system.maintenance.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-wrench',
           'text'=>__('Maintenance'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.maintenance.index',
                   'url'=> route('system.maintenance.index'),
                   'text'=> __('View Maintenance'),
               ],

               'Create'=> [
                   'permission'=> 'system.maintenance.create',
                   'url'=> route('system.maintenance.create'),
                   'text'=> __('Create Maintenance'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.maintenance.index',
                   'url'=> route('system.maintenance.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Maintenance')
               ],

           ]
       ]
       ]


       ];




$menu['Project'] = [


 'icon'=>'fa fa-rss',
           'text'=>__('Project'),
           'sub'=>[


 'Department'=>[
           'permission'=> [
                'system.department.index',
                'system.department.create',
                'system.department.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-tasks',
           'text'=>__('Department'),
           'sub'=>[




               'View'=> [
                   'permission'=> 'system.department.index',
                   'url'=> route('system.department.index'),
                   'text'=> __('View department'),
               ],

               'Create'=> [
                   'permission'=> 'system.department.create',
                   'url'=> route('system.department.create'),
                   'text'=> __('Create department'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.department.index',
                   'url'=> route('system.department.index',['withTrashed'=>1]),
                   'text'=> __('Trashed department')
               ],
]
           ],

        'Contract'  => [

           'permission'=> [
                'system.contract.index',
                'system.contract.create',
                'system.contract.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-bus',
           'text'=>__('contract'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.contract.index',
                   'url'=> route('system.contract.index'),
                   'text'=> __('View contract'),
               ],

               'Create'=> [
                   'permission'=> 'system.contract.create',
                   'url'=> route('system.contract.create'),
                   'text'=> __('Create contract'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.contract.index',
                   'url'=> route('system.contract.index',['withTrashed'=>1]),
                   'text'=> __('Trashed contract')
               ],


        ]
             ],

'Project'=>[
 'permission'=> [
                'system.project.index',
                'system.project.create',
                'system.project.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-dollar',
           'text'=>__('Project'),
           'sub'=>[
 'View'=> [
                   'permission'=> 'system.project.index',
                   'url'=> route('system.project.index'),
                   'text'=> __('View Project'),
               ],

               'Create'=> [
                   'permission'=> 'system.project.create',
                   'url'=> route('system.project.create'),
                   'text'=> __('Create Project'),
               ],

               'Deleted'=>[
                   'aClass'=> 'color-red',
                   'permission'=> 'system.project.index',
                   'url'=> route('system.project.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Project')
               ],
               ],
],

]           ];






$menu['Accounts'] = [


 'icon'=>'fa fa-dollar',
           'text'=>__('Accounts'),
           'sub'=>[



   'Revenue'=>[
'permission'=> [
                'system.profit.index',
                'system.profit.create',
                'system.profit.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-dollar',
           'text'=>__('Revenue'),
           'sub'=>[

 'Causes'=>[
           'permission'=> [
                'system.profit.index',
                'system.profit.create',
                'system.profit.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-dollar',
           'text'=>__('Causes'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.profit.index',
                   'url'=> route('system.profit.index'),
                   'text'=> __('View Revenue Causes'),
               ],

               'Create'=> [
                   'permission'=> 'system.profit.create',
                   'url'=> route('system.profit.create'),
                   'text'=> __('Create Revenue Causes'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.profit.index',
                   'url'=> route('system.profit.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Revenue Causes')
               ],

           ]
       ],



        'Revenue'=>[

           'permission'=> [
                'system.profits.index',
                'system.profits.create',
                'system.profits.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-money',
           'text'=>__('Revenues'),
           'sub'=>[
             'View'=> [
                   'permission'=> 'system.profits.index',
                   'url'=> route('system.profits.index'),
                   'text'=> __('View Revenues'),
               ],

               'Create'=> [
                   'permission'=> 'system.profits.create',
                   'url'=> route('system.profits.create'),
                   'text'=> __('Create Revenue'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.profits.index',
                   'url'=> route('system.profits.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Revenue')
               ],

           ]
        ],
]],


'Expenses'=>[
           'permission'=> [
                'system.expenses.index',
                'system.expenses.create',
                'system.expenses.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-money',
           'text'=>__('Expenses'),
           'sub'=>[



 'Causes'=>[
  'permission'=> [
                'system.expense.index',
                'system.expense.create',
                'system.expense.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-dollar',
           'text'=>__(' Causes'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.expense.index',
                   'url'=> route('system.expense.index'),
                   'text'=> __('View Expense Causes'),
               ],

               'Create'=> [
                   'permission'=> 'system.expense.create',
                   'url'=> route('system.expense.create'),
                   'text'=> __('Create Expense Causes'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.expense.index',
                   'url'=> route('system.expense.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Expense Causes')
               ],

           ],


],



'Expense'=>[

   'permission'=> [
                'system.expense.index',
                'system.expense.create',
                'system.expense.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-dollar',
           'text'=>__('Expenses'),
           'sub'=>[

 'View'=> [
                   'permission'=> 'system.expenses.index',
                   'url'=> route('system.expenses.index'),
                   'text'=> __('View Expenses'),
               ],

               'Create'=> [
                   'permission'=> 'system.expenses.create',
                   'url'=> route('system.expenses.create'),
                   'text'=> __('Create Expense'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.expenses.index',
                   'url'=> route('system.expenses.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Expenses')
               ],

]
]
 ],


]
]
];


        $menu['Items'] = [
           'permission'=> [
                'system.item.index',
                'system.item.create',
                'system.item.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-product-hunt',
           'text'=>__('Items'),
           'sub'=>[

           'Items' => [
           'permission'=> [
                'system.category.index',
                'system.category.create',
                'system.category.edit',
                ],
           'class'=>'',
           'icon'=>' fa fa-object-group',
           'text'=>__('Item categories'),
           'sub'=>[

               'View'=> [
                   'permission'=> 'system.category.index',
                   'url'=> route('system.category.index'),
                   'text'=> __('View category'),
               ],

               'Create'=> [
                   'permission'=> 'system.category.create',
                   'url'=> route('system.category.create'),
                   'text'=> __('Create category'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.category.index',
                   'url'=> route('system.category.index',['withTrashed'=>1]),
                   'text'=> __('Trashed category')
               ],

           ]
       ],

'Report'=> [
                   'permission'=> 'system.item.report',
                   'url'=> route('system.item.report'),
                   'text'=> __('Item Report'),
               ],

               'View'=> [
                   'permission'=> 'system.item.index',
                   'url'=> route('system.item.index'),
                   'text'=> __('View Items'),
               ],

               'Create'=> [
                   'permission'=> 'system.item.create',
                   'url'=> route('system.item.create'),
                   'text'=> __('Create Item'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.item.index',
                   'url'=> route('system.item.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Items')
               ],

           ]
       ];





$menu['Quotations'] = [
           'permission'=> [
                'system.quotations.index',
                'system.quotations.create',
                'system.quotations.edit',
                ],
           'class'=>'',
           'icon'=>'fa fa-quote-left',
           'text'=>__('Quotations'),
           'sub'=>[




               'View'=> [
                   'permission'=> 'system.quotations.index',
                   'url'=> route('system.quotations.index'),
                   'text'=> __('View Quotations'),
               ],

               'Create'=> [
                   'permission'=> 'system.quotations.create',
                   'url'=> route('system.quotations.create'),
                   'text'=> __('Create Quotations'),
               ],

               [
                   'aClass'=> 'color-red',
                   'permission'=> 'system.quotations.index',
                   'url'=> route('system.quotations.index',['withTrashed'=>1]),
                   'text'=> __('Trashed Quotations')
               ],


]
           ];





$menu['System'] = [
           'permission'=> [
                'system.setting.index',
                'system.activity-log.index'
                ],
           'class'=>'',
           'icon'=>'fa fa-cogs',
           'text'=>__('System'),
           'sub'=>[

               'Setting'=> [
                   'permission'=> 'system.setting.index',
                   'icon'=> 'fa fa-cog',
                   'url'=> route('system.setting.index'),
                   'text'=> __('Setting'),
               ],

           ]
       ];

@endphp

@foreach($menu as $onemenu)
    {!! generateMenu($onemenu) !!}
@endforeach