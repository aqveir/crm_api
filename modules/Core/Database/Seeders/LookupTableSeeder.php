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
            [ //Industry Type
                'key' => 'industry_type',
                'display_value' => 'Industry Type',
                'description' => 'Type of Industry to be used on the tables for organization, configurations and alikes.',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'industry_type_vanilla',
                        'display_value' => 'Vanilla CRM',
                    ],
                    [
                        'key' => 'industry_type_retail',
                        'display_value' => 'Retail Industry',
                    ],
                    [
                        'key' => 'industry_type_real_estate',
                        'display_value' => 'Real Estate',
                    ],
                    [
                        'key' => 'industry_type_travel',
                        'display_value' => 'Travel & Tourisum',
                    ]
                ]
            ],
            [ //Entity Type
                'key' => 'entity_type',
                'display_value' => 'Entity Type',
                'description' => 'Type or Entity to be used on the tables for notes, feedback and alikes.',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'entity_type_contact',
                        'display_value' => 'Contact Entity',
                    ],
                    [
                        'key' => 'entity_type_contact_address',
                        'display_value' => 'Contact Address Entity',
                    ],
                    [
                        'key' => 'entity_type_inventory',
                        'display_value' => 'Inventory Entity',
                    ],
                    [
                        'key' => 'entity_type_service_request',
                        'display_value' => 'Service Request',
                    ],
                    [
                        'key' => 'entity_type_event',
                        'display_value' => 'Event',
                    ]
                ]
            ],
            [ //Data Type [Used in Org Interface types & Preferences types]
                'key' => 'data_type',
                'display_value' => 'Interface Type',
                'description' => 'Type of Interface',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'data_type_string',
                        'display_value' => 'String',
                    ],
                    [
                        'key' => 'data_type_number',
                        'display_value' => 'Number',
                    ],
                    [
                        'key' => 'data_type_boolean',
                        'display_value' => 'Boolean',
                    ],
                    [
                        'key' => 'data_type_json',
                        'display_value' => 'JSON Object',
                    ],
                    [
                        'key' => 'data_type_lookup',
                        'display_value' => 'DropDown Select',
                    ],
                    [
                        'key' => 'data_type_location',
                        'display_value' => 'Location',
                    ],
                    [
                        'key' => 'data_type_external',
                        'display_value' => 'External Data',
                    ]
                ]
            ],
            [ //User Availavility Status
                'key' => 'user_status',
                'display_value' => 'User Status',
                'description' => 'Status of Users',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'user_status_online',
                        'display_value' => 'Online',
                    ],
                    [
                        'key' => 'user_status_offline',
                        'display_value' => 'Offline',
                    ],
                    [
                        'key' => 'user_status_busy',
                        'display_value' => 'Busy',
                    ],
                    [
                        'key' => 'user_status_away',
                        'display_value' => 'Away',
                    ],
                ]
            ],
            [ //Communication Direction
                'key' => 'communication_direction',
                'display_value' => 'Communication Direction Type',
                'description' => 'Status used to display Communication Direction',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'communication_direction_incoming',
                        'display_value' => 'In-coming OR Inbound',
                    ],
                    [
                        'key' => 'communication_direction_outgoing',
                        'display_value' => 'Out-going or Outbound',
                    ],
                ]
            ],
            [ //Telephony Call Status
                'key' => 'telephony_call_status_type',
                'display_value' => 'Telephony Call Status Type',
                'description' => 'Status used and displayed for Telephony Calls',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'telephony_call_status_type_queued',
                        'display_value' => 'Queued',
                    ],
                    [
                        'key' => 'telephony_call_status_type_in_progress',
                        'display_value' => 'In Progress',
                    ],
                    [
                        'key' => 'telephony_call_status_type_busy',
                        'display_value' => 'Busy',
                    ],
                    [
                        'key' => 'telephony_call_status_type_no_answer',
                        'display_value' => 'No Answer',
                    ],
                    [
                        'key' => 'telephony_call_status_type_failed',
                        'display_value' => 'Failed',
                    ],
                    [
                        'key' => 'telephony_call_status_type_completed',
                        'display_value' => 'Completed',
                    ],                    
                ]
            ],
            [ //Account Type
                'key' => 'account_type',
                'display_value' => 'Account Type',
                'description' => 'Account Type used',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'account_type_default',
                        'display_value' => 'Default',
                    ],
                    [
                        'key' => 'account_type_internal',
                        'display_value' => 'Internal',
                    ],
                    [
                        'key' => 'account_type_external',
                        'display_value' => 'External',
                    ],
                    [
                        'key' => 'account_type_contact',
                        'display_value' => 'Contact',
                    ],
                    [
                        'key' => 'account_type_partner',
                        'display_value' => 'Partner',
                    ],
                    [
                        'key' => 'account_type_reseller',
                        'display_value' => 'Reseller',
                    ],
                ]
            ],
            [ //Contact Type
                'key' => 'contact_type',
                'display_value' => 'Contact Types',
                'description' => 'Types of the Contact',
                'values' => [
                    [
                        'key' => 'contact_type_default',
                        'display_value' => 'Default OR Guest',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_type_customer',
                        'display_value' => 'Customer',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_type_vendor',
                        'display_value' => 'Vendor',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_type_internal',
                        'display_value' => 'Emploee OR Internal',
                        'is_editable' => false,
                    ],
                ]
            ],
            [ //Contact Gender
                'key' => 'contact_gender',
                'display_value' => 'Gender',
                'description' => 'Gender of the Contact',
                'values' => [
                    [
                        'key' => 'contact_gender_male',
                        'display_value' => 'Male',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_gender_female',
                        'display_value' => 'Female',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_gender_others',
                        'display_value' => 'Others',
                    ]
                ]
            ],
            [ //Contact Detail Type
                'key' => 'contact_detail_type',
                'display_value' => 'Contact Detail Types',
                'description' => 'Types of the Contact Details',
                'values' => [
                    [
                        'key' => 'contact_detail_type_email',
                        'display_value' => 'Email',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_detail_type_phone',
                        'display_value' => 'Phone',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_detail_type_socialhandle',
                        'display_value' => 'Social Site Handle',
                    ],
                    [
                        'key' => 'contact_detail_type_webpage',
                        'display_value' => 'Web Page',
                    ],
                ]
            ],
            [ //Contact Detail SubTypes
                'key' => 'contact_detail_subtype',
                'display_value' => 'Contact Detail SubTypes',
                'description' => 'SubTypes of the Contact Details',
                'values' => [
                    [
                        'key' => 'contact_detail_subtype_email_work',
                        'display_value' => 'Work',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_detail_subtype_email_personal',
                        'display_value' => 'Personal',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_detail_subtype_phone_mobile',
                        'display_value' => 'Phone-Mobile',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_detail_subtype_phone_landline',
                        'display_value' => 'Phone-Landline',
                    ],
                    [
                        'key' => 'contact_detail_subtype_socialhandle_twitter',
                        'display_value' => 'Twitter',
                    ],
                    [
                        'key' => 'contact_detail_subtype_socialhandle_facebook',
                        'display_value' => 'Facebook',
                    ],
                    [
                        'key' => 'contact_detail_subtype_socialhandle_instagram',
                        'display_value' => 'Instagram',
                    ],
                    [
                        'key' => 'contact_detail_subtype_webpage_website',
                        'display_value' => 'Website',
                    ],
                    [
                        'key' => 'contact_detail_subtype_webpage_facebook_profile',
                        'display_value' => 'Facebook Profile',
                    ],
                ]
            ],
            [ //Contact Address Type
                'key' => 'contact_address_type',
                'display_value' => 'Contact Address Types',
                'description' => 'Types of the Contact Address',
                'values' => [
                    [
                        'key' => 'contact_address_type_home',
                        'display_value' => 'Home',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'contact_address_type_work',
                        'display_value' => 'Work',
                    ],
                    [
                        'key' => 'contact_address_type_other',
                        'display_value' => 'Other',
                    ],
                ]
            ],
            [ //Service Request Category
                'key' => 'service_request_category',
                'display_value' => 'Service Request Category',
                'description' => 'Category of the Service Requests',
                'values' => [
                    [
                        'key' => 'service_request_category_lead',
                        'display_value' => 'Lead',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'service_request_category_opportunity',
                        'display_value' => 'Opportunity',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'service_request_category_support',
                        'display_value' => 'Support',
                        'is_editable' => false,
                    ]
                ]
            ],
            [ //Service Request Type
                'key' => 'service_request_type',
                'display_value' => 'Service Request Types',
                'description' => 'Types of Service Request',
                'values' => [
                    [
                        'key' => 'service_request_type_default',
                        'display_value' => 'Default',
                        'is_editable' => false,
                        'order' => 0
                    ]
                ]
            ], 
            [ //Service Request Status
                'key' => 'service_request_status',
                'display_value' => 'Service Request Status',
                'description' => 'Status of Service Request',
                'values' => [
                    [
                        'key' => 'service_request_status_new',
                        'display_value' => 'New',
                        'is_editable' => false,
                        'order' => 0
                    ],
                    [
                        'key' => 'service_request_status_active',
                        'display_value' => 'Active',
                        'is_editable' => false,
                        'order' => 5
                    ],
                    [
                        'key' => 'service_request_status_closed_won',
                        'display_value' => 'Closed [Won]',
                        'is_editable' => false,
                        'order' => 10
                    ],
                    [
                        'key' => 'service_request_status_closed_lost',
                        'display_value' => 'Closed [Lost]',
                        'is_editable' => false,
                        'order' => 11
                    ],
                    [
                        'key' => 'service_request_status_archived',
                        'display_value' => 'Archived',
                        'is_editable' => false,
                        'order' => 15
                    ]
                ]
            ],
            [ //Service Request Stage
                'key' => 'service_request_stage',
                'display_value' => 'Service Request Stages',
                'description' => 'Stages of Service Request',
                'values' => [
                    [
                        'key' => 'service_request_stage_new',
                        'display_value' => 'New',
                        'is_editable' => false,
                        'order' => 0
                    ],
                    [
                        'key' => 'service_request_stage_closed',
                        'display_value' => 'Closed',
                        'is_editable' => false,
                        'order' => 20
                    ],
                ]
            ],             
            [ //Service Request Activity Type
                'key' => 'service_request_activity_type',
                'display_value' => 'Service Request Activities',
                'description' => 'List of Activities of Service Request',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'service_request_activity_type_task',
                        'display_value' => 'Task',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'service_request_activity_type_event',
                        'display_value' => 'Event/Meeting',
                        'is_editable' => false,
                    ],
                ]
            ],
            [ //Service Request Activity - Task SubTypes
                'key' => 'service_request_comm_type',
                'display_value' => 'Service Request - Communication Types',
                'description' => 'List of Sub Types for the Task Activity OR Communication Types of Service Request',
                'values' => [
                    [
                        'key' => 'comm_type_call',
                        'display_value' => 'Call',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'comm_type_sms',
                        'display_value' => 'SMS',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'comm_type_email',
                        'display_value' => 'Email',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'comm_type_other',
                        'display_value' => 'Other',
                        'is_editable' => false,
                    ],
                ]
            ],
            [ //Service Request Activity - Task Priority
                'key' => 'service_request_activity_type_task_priority',
                'display_value' => 'Task Priority',
                'description' => 'List of priorities for the Task Activity of Service Request',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'priority_low',
                        'display_value' => 'Low',
                        'order' => 1,
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'priority_normal',
                        'display_value' => 'Normal',
                        'order' => 2,
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'priority_high',
                        'display_value' => 'High',
                        'order' => 3,
                        'is_editable' => false,
                    ]
                ]
            ],
            [ //Service Request Activity - Task Status
                'key' => 'service_request_activity_type_task_status',
                'display_value' => 'Task Status',
                'description' => 'List of status for the Task Activity of Service Request',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'task_status_not_started',
                        'display_value' => 'Not Started',
                        'order' => 1,
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'task_status_started',
                        'display_value' => 'In Progress',
                        'order' => 2,
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'task_status_completed',
                        'display_value' => 'Completed',
                        'order' => 3,
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'task_status_awaiting_inputs',
                        'display_value' => 'Hold / Awaiting Inputs',
                        'order' => 4,
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'task_status_deferred',
                        'display_value' => 'Deferred',
                        'order' => 5,
                        'is_editable' => false,
                    ],
                ]
            ],       
            [ //Service Request Activity - Event SubTypes
                'key' => 'service_request_activity_type_event_subtype',
                'display_value' => 'Service Request Event Activity Sub-Types',
                'description' => 'List of Sub Types for the Event Activity of Service Request',
                'values' => [
                    [
                        'key' => 'service_request_activity_type_event_subtype_meeting',
                        'display_value' => 'Meeting',
                        'is_editable' => false,
                    ],
                ]
            ],
            [ //Service Request Activity - Participants Type
                'key' => 'service_request_activity_participant_type',
                'display_value' => 'Service Request Activity - Participant Types',
                'description' => 'List of Participant Types for the Activities of Service Request',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'service_request_activity_participant_type_user',
                        'display_value' => 'Internal User',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'service_request_activity_participant_type_contact',
                        'display_value' => 'Contact',
                        'is_editable' => false,
                    ],
                ]
            ],
            [ //Service Request Source - Channel
                'key' => 'service_request_source_channel',
                'display_value' => 'Service Request - Source Channel',
                'description' => 'List of channels that classify the Service Request sources.',
                'values' => [
                    [
                        'key' => 'service_request_source_channel_contact',
                        'display_value' => 'Contact',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'service_request_source_channel_user',
                        'display_value' => 'User',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'service_request_source_channel_text',
                        'display_value' => 'Text Data',
                        'is_editable' => false,
                    ],
                ]
            ],
            [ //Service Request - Communication Person Type
                'key' => 'service_request_communication_person_type',
                'display_value' => 'Service Request Event Activity - Participant Types',
                'description' => 'List of Participant Types for the Communication of Service Request',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'communication_person_type_system',
                        'display_value' => 'System',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'communication_person_type_user',
                        'display_value' => 'Internal User',
                        'is_editable' => false,
                    ],
                    [
                        'key' => 'communication_person_type_contact',
                        'display_value' => 'Contact',
                        'is_editable' => false,
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
            [ //User Type
                'key' => 'user_type',
                'display_value' => 'User Type',
                'description' => 'Type of Users',
                'is_editable' => false,
                'values' => [
                    [
                        'key' => 'user_type_internal',
                        'display_value' => 'Employee',
                    ],
                    [
                        'key' => 'user_type_external',
                        'display_value' => 'Agent / Channel Partner',
                    ]
                ]
            ],
        ];
    }

} //Class ends
