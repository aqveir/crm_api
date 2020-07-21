<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class PrivilegesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $privileges = [
            [
                'key' => 'list_all_organizations',
                'display_value' => 'Show All Organizations',
            ],
            [
                'key' => 'read_organization_data',
                'display_value' => 'Read/View Organization Data',
            ],
            [
                'key' => 'add_organization_data',
                'display_value' => 'Add Organization Data',
            ],
            [
                'key' => 'edit_organization_data',
                'display_value' => 'Edit/Amend Organization Data',
            ],
            [
                'key' => 'list_all_organization_stores',
                'display_value' => 'Show All Stores in the Organization',
            ],
            [
                'key' => 'add_new_stores_data',
                'display_value' => 'Add Store Data',
            ],
            [
                'key' => 'edit_stores_data',
                'display_value' => 'Edit/Amend Store Data',
            ],
            [
                'key' => 'list_all_organization_catalogue',
                'display_value' => 'Show Product Catalogue in the Organization',
            ],
            [
                'key' => 'add_new_catalogue_data',
                'display_value' => 'Add Product Catalogue',
            ],
            [
                'key' => 'edit_catalogue_data',
                'display_value' => 'Edit/Amend Product Catalogue',
            ],

            [
                'key' => 'list_all_organization_customers',
                'display_value' => 'Show Customers in the Organization',
            ],
            [
                'key' => 'add_new_customer_data',
                'display_value' => 'Add Customer Record',
            ],
            [
                'key' => 'edit_customer_data',
                'display_value' => 'Edit/Amend Customer Record',
            ],
            [
                'key' => 'delete_customer_data',
                'display_value' => 'Delete Customer Record',
            ],
            [
                'key' => 'show_customer_unmasked_data',
                'display_value' => 'Show Customer Record (Unmasked)',
            ],
            [
                'key' => 'show_customer_masked_data',
                'display_value' => 'Show Customer Record (Masked)',
            ],

            [ //add_new_note
                'key' => 'add_new_note',
                'display_value' => 'Add Note',
            ],
            [ //delete_note
                'key' => 'delete_note',
                'display_value' => 'Delete Note',
            ],

            [ //add_new_document
                'key' => 'add_new_document',
                'display_value' => 'Add Document',
            ],
            [ //delete_document
                'key' => 'delete_document',
                'display_value' => 'Delete Document',
            ],
        ];

        foreach ($privileges as $privilege) {
            $response = factory(\Modules\Core\Models\Privilege\Privilege::class)->create([
                'key' => $privilege['key'],
                'display_value' => $privilege['display_value'],
            ]);
        } //Loop ends
    } //Function ends
} //Class ends
