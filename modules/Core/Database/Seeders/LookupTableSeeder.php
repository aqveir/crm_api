<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class LookupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $lookups = $this->data();
        foreach ($lookups as $lookup) {
            $response = factory(\Modules\Core\Models\Lookup\Lookup::class)->create([
                'key' => $lookup['key'],
                'display_value' => $lookup['display_value'],
            ]);

            $lookupvalues = $lookup['values'];
            foreach ($lookupvalues as $lookupvalue) {
                $lookupvalue['lookup_id']=$response['id'];
                factory(\Modules\Core\Models\Lookup\LookupValue::class)->create($lookupvalue);
            } //Loop ends
        } //Loop ends
    } //Function ends


    private function data() {
        return [
            [ //Entity Type
                'key' => 'entity_type',
                'display_value' => 'Entity Type',
                'description' => 'Type or Entity to be used on the tables for notes, feedback and alikes.',
                'values' => [
                    [
                        'key' => 'entity_type_customer',
                        'display_value' => 'Customer Entity',
                    ],
                    [
                        'key' => 'entity_type_catalogue_product',
                        'display_value' => 'Product Entity',
                    ],
                    [
                        'key' => 'entity_type_order',
                        'display_value' => 'Order Entity',
                    ],
                    [
                        'key' => 'entity_type_order_product',
                        'display_value' => 'Order-Product Entity',
                    ]
                ]
            ],
            [ //Organization Configuration Type
                'key' => 'organization_interface_type',
                'display_value' => 'Interface Type',
                'description' => 'Type of Interface',
                'values' => [
                    [
                        'key' => 'organization_interface_type_string',
                        'display_value' => 'String',
                    ],
                    [
                        'key' => 'organization_interface_type_number',
                        'display_value' => 'Number',
                    ],
                    [
                        'key' => 'organization_interface_type_boolean',
                        'display_value' => 'Boolean',
                    ],
                    [
                        'key' => 'organization_interface_type_json',
                        'display_value' => 'JSON Object',
                    ],
                    [
                        'key' => 'organization_interface_type_dropdown',
                        'display_value' => 'DropDown Select',
                    ]
                ]
            ],
            [ //Backend User Status
                'key' => 'backend_user_status',
                'display_value' => 'Backend User Status',
                'description' => 'Status of Backend Users',
                'values' => [
                    [
                        'key' => 'backend_user_status_online',
                        'display_value' => 'Online',
                    ],
                    [
                        'key' => 'backend_user_status_offline',
                        'display_value' => 'Offline',
                    ],
                    [
                        'key' => 'backend_user_status_busy',
                        'display_value' => 'Busy',
                    ],
                    [
                        'key' => 'backend_user_status_available',
                        'display_value' => 'Available',
                    ],
                ]
            ],
            [ //Store Type
                'key' => 'store_type',
                'display_value' => 'Store Type',
                'description' => 'Type of the Store/Outlet',
                'values' => [
                    [
                        'key' => 'store_type_warehouse',
                        'display_value' => 'Warehouse',
                    ],
                    [
                        'key' => 'store_type_retail',
                        'display_value' => 'Retail Shop',
                    ],
                    [
                        'key' => 'store_type_distribution_center',
                        'display_value' => 'Distribution Center',
                    ],
                    [
                        'key' => 'store_type_virtual_digital',
                        'display_value' => 'Virtual Digital Store',
                    ],
                ]
            ],
            [ //Customer Type
                'key' => 'customer_type',
                'display_value' => 'Customer Types',
                'description' => 'Types of the Customer',
                'values' => [
                    [
                        'key' => 'customer_type_guest',
                        'display_value' => 'Guest',
                    ],
                    [
                        'key' => 'customer_type_retailer',
                        'display_value' => 'Retailer',
                    ],
                    [
                        'key' => 'customer_type_wholesaler',
                        'display_value' => 'Wholesaler',
                    ],
                    [
                        'key' => 'customer_type_distributor',
                        'display_value' => 'Distributor',
                    ],
                    [
                        'key' => 'customer_type_business',
                        'display_value' => 'Business',
                    ]
                ]
            ],
            [ //Customer Gender
                'key' => 'customer_gender',
                'display_value' => 'Gender',
                'description' => 'Gender of the Customer',
                'values' => [
                    [
                        'key' => 'customer_gender_male',
                        'display_value' => 'Male',
                    ],
                    [
                        'key' => 'customer_gender_female',
                        'display_value' => 'Female',
                    ],
                    [
                        'key' => 'customer_gender_others',
                        'display_value' => 'Others',
                    ]
                ]
            ],
            [ //Customer Detail Type
                'key' => 'customer_detail_type',
                'display_value' => 'Customer Detail Types',
                'description' => 'Types of the Customer Details',
                'values' => [
                    [
                        'key' => 'customer_detail_type_email',
                        'display_value' => 'Email',
                    ],
                    [
                        'key' => 'customer_detail_type_phone',
                        'display_value' => 'Phone',
                    ],
                    [
                        'key' => 'customer_detail_type_socialhandle',
                        'display_value' => 'Social Site Handle',
                    ],
                    [
                        'key' => 'customer_detail_type_webpage',
                        'display_value' => 'Web Page',
                    ],
                ]
            ],
            [ //Customer Detail SubTypes
                'key' => 'customer_detail_type_sub',
                'display_value' => 'Customer Detail SubTypes',
                'description' => 'SubTypes of the Customer Details',
                'values' => [
                    [
                        'key' => 'customer_detail_type_email_work',
                        'display_value' => 'Work',
                    ],
                    [
                        'key' => 'customer_detail_type_email_personal',
                        'display_value' => 'Personal',
                    ],
                    [
                        'key' => 'customer_detail_type_phone_mobile',
                        'display_value' => 'Phone-Mobile',
                    ],
                    [
                        'key' => 'customer_detail_type_phone_landline',
                        'display_value' => 'Phone-Landline',
                    ],
                    [
                        'key' => 'customer_detail_type_socialhandle_twitter',
                        'display_value' => 'Twitter',
                    ],
                    [
                        'key' => 'customer_detail_type_socialhandle_facebook',
                        'display_value' => 'Facebook',
                    ],
                    [
                        'key' => 'customer_detail_type_socialhandle_instagram',
                        'display_value' => 'Instagram',
                    ],
                    [
                        'key' => 'customer_detail_type_webpage_website',
                        'display_value' => 'Website',
                    ],
                    [
                        'key' => 'customer_detail_type_webpage_facebook_profile',
                        'display_value' => 'Facebook Profile',
                    ],
                ]
            ],
            [ //Customer Address Type
                'key' => 'customer_address_type',
                'display_value' => 'Customer Address Types',
                'description' => 'Types of the Customer Address',
                'values' => [
                    [
                        'key' => 'customer_address_type_home',
                        'display_value' => 'Home',
                    ],
                    [
                        'key' => 'customer_address_type_work',
                        'display_value' => 'Work',
                    ],
                    [
                        'key' => 'customer_address_type_other',
                        'display_value' => 'Other',
                    ],
                ]
            ],
            [ //Order Type
                'key' => 'order_type',
                'display_value' => 'Order Type',
                'description' => 'Type of the Order',
                'values' => [
                    [
                        'key' => 'order_type_ecommerce',
                        'display_value' => 'Ecommerce',
                    ],
                    [
                        'key' => 'order_type_subscription',
                        'display_value' => 'Subscription',
                    ]
                ]
            ],
            [ //Order Status
                'key' => 'order_status',
                'display_value' => 'Order Status',
                'description' => 'Status of the Order',
                'values' => [
                    [
                        'key' => 'order_status_new',
                        'display_value' => 'New',
                    ],
                    [
                        'key' => 'order_status_processing',
                        'display_value' => 'Processing',
                    ],
                    [
                        'key' => 'order_status_accepted',
                        'display_value' => 'Accepted',
                    ],
                    [
                        'key' => 'order_status_rejected',
                        'display_value' => 'Rejected',
                    ],
                    [
                        'key' => 'order_status_missed',
                        'display_value' => 'Missed',
                    ],
                    [
                        'key' => 'order_status_dispatched',
                        'display_value' => 'Dispatched',
                    ],
                    [
                        'key' => 'order_status_delivered',
                        'display_value' => 'Delivered',
                    ],
                    [
                        'key' => 'order_status_completed',
                        'display_value' => 'Completed',
                    ],
                ]
            ],
            [ //Order Source
                'key' => 'order_source',
                'display_value' => 'Order Source',
                'description' => 'Source of the Order',
                'values' => [
                    [
                        'key' => 'order_source_shop',
                        'display_value' => 'Shop',
                    ],
                    [
                        'key' => 'order_source_web',
                        'display_value' => 'Digital (Web)',
                    ],
                    [
                        'key' => 'order_source_mobile_android',
                        'display_value' => 'Digital (Mobile-Android)',
                    ],
                    [
                        'key' => 'order_source_mobile_ios',
                        'display_value' => 'Digital (Mobile-iOS)',
                    ]
                ]
            ],            
            [ //Order Product Fulfillment Status
                'key' => 'order_product_fulfillment_status',
                'display_value' => 'Order Product Status',
                'description' => 'Status of the Products in an Order',
                'values' => [
                    [
                        'key' => 'order_product_fulfillment_status_new',
                        'display_value' => 'New',
                    ],
                    [
                        'key' => 'order_product_fulfillment_status_processing',
                        'display_value' => 'Processing',
                    ],
                    [
                        'key' => 'order_product_fulfillment_status_picked',
                        'display_value' => 'Picked',
                    ],
                    [
                        'key' => 'order_product_fulfillment_status_packed',
                        'display_value' => 'Packed',
                    ],
                    [
                        'key' => 'order_product_fulfillment_status_dispatched',
                        'display_value' => 'Dispatched',
                    ],
                    [
                        'key' => 'order_product_fulfillment_status_delivered',
                        'display_value' => 'Delivered',
                    ],
                ]
            ],
        ];
    }

} //Class ends