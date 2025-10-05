<?php

namespace Modules\Preference\Database\Seeders;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Seeder;

class PreferenceMetaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Get Meta Preferences Data
        $metaPreferences = $this->dataMetaPreferences();

        foreach ($metaPreferences as $metaPreference) {
            if (!empty($metaPreference)) {
                $industryType = $metaPreference['industry_key'];
                $preferences = $metaPreference['data'];

                foreach ($preferences as $preference) {
                    //Create Meta Data
                    $response = factory(\Modules\Preference\Models\Meta\PreferenceMeta::class)->create([
                        'name'              => $preference['name'],
                        'industry_key'      => $industryType,
                        'display_value'     => array_key_exists('display_value', $preference)?$preference['display_value']:null,
                        'description'       => array_key_exists('description', $preference)?$preference['description']:null,
                        'type_key'              => $preference['type']['value'],
                        'filter_json'       => array_key_exists('filter_json', $preference)?json_encode($preference['filter_json']):null,
                        'is_maximum'        => array_key_exists('is_maximum', $preference)?$preference['is_maximum']:0,
                        'is_minimum'        => array_key_exists('is_minimum', $preference)?$preference['is_minimum']:0,
                        'data_json'         => array_key_exists('data_json', $preference)?json_encode($preference['data_json']):null,
                        'is_multiple'       => array_key_exists('is_multiple', $preference)?$preference['is_multiple']:false,
                        'keywords'          => array_key_exists('keywords', $preference)?$preference['keywords']:null,
                        'order'             => array_key_exists('order', $preference)?$preference['order']:0,
                    ]);
                } //Loop
            } //End if
           
        } //Loop ends
    }

    private function dataMetaPreferences() {
        $data = [];
        array_push($data, $this->dataMetaPreferencesVanilla());
        array_push($data, $this->dataMetaPreferencesRealEstate());
        array_push($data, $this->dataMetaPreferencesTravel());
        array_push($data, $this->dataMetaPreferencesRetail());

        return $data;
    } //Function ends


    /**
     * Preferences Meta Data - Vanilla
     */
    private function dataMetaPreferencesVanilla()
    {
        $data = [];
        $data['industry_key'] = 'industry_type_vanilla';
        $data['data'] = [
            [ //Estimated Price (max)
                'name' => 'max_estimated_price',
                'display_value' => 'Estimated Price (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'sale,price',
                'order' => 6,
            ],
            [ //Estimated Price (min)
                'name' => 'min_estimated_price',
                'display_value' => 'Estimated Price (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'sale,price',
                'order' => 5,
            ],
            [ //Location
                'name' => 'lead_location',
                'display_value' => 'Location',
                'type' => ['value' => 'data_type_location'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 1,
            ],
        ];
        return $data; 
    } //Function ends


    /**
     * Preferences Meta Data - Real Estate
     */
    private function dataMetaPreferencesRealEstate()
    {
        $data = [];
        $data['industry_key'] = 'industry_type_real_estate';
        $data['data'] = [
            [ //Age of Property (Max)
                'name' => 'max_age_of_property',
                'display_value' => 'Age of Property (Max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'age,property,years,yrs',
                'order' => 0,
            ],
            [ //Age of Property (Min)
                'name' => 'min_age_of_property',
                'display_value' => 'Age of Property (Min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'age,property,years,yrs',
                'order' => 0,
            ],
            [ //Air Conditioners (max)
                'name' => 'max_air_conditioners',
                'display_value' => 'Air Conditioners (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'air,conditioners',
                'order' => 0,
            ],
            [ //Air Conditioners (min)
                'name' => 'min_air_conditioners',
                'display_value' => 'Air Conditioners (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'air,conditioners',
                'order' => 0
            ], 
            [ //Bachelor or Family
                'name' => 'bachelor_or_family',
                'display_value' => 'Bachelor or Family',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'family',
                'order' => 0,
                'data_json' => [
                    'name' => 'bachelor_or_family',
                    'display_value' => 'Bachelor_or_Family',
                    'values' => [
                        ['value' => 'Bachelors OK', 'display_value' => 'Bachelor'],
                        ['value' => 'Families Only', 'display_value' => 'Family Only']
                    ]
                ]
            ],
            [ //Badminton Court
                'name' => 'badminton_court',
                'display_value' => 'Badminton Court',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'badminton,court',
                'order' => 0,
            ],
            [ //Balconies (max)
                'name' => 'max_balconies',
                'display_value' => 'Balconies (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'balconies',
                'order' => 0,
            ],
            [ //Balconies (min)
                'name' => 'min_balconies',
                'display_value' => 'Balconies (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'balconies',
                'order' => 0,
            ],
            [ //Basketball Court
                'name' => 'basketball_court',
                'display_value' => 'Basketball Court',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'basketball,court',
                'order' => 0,
            ],
            [ //Bathrooms (max)
                'name' => 'max_bathrooms',
                'display_value' => 'Bathrooms (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'bathrooms',
                'order' => 0,
            ],
            [ //Bathrooms (min)
                'name' => 'min_bathrooms',
                'display_value' => 'Bathrooms (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'bathrooms',
                'order' => 0,
            ],
            [ //Beds (max)
                'name' => 'max_beds',
                'display_value' => 'Beds (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'beds',
                'order' => 0,
            ],
            [ //Beds (min)
                'name' => 'min_beds',
                'display_value' => 'Beds (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'beds',
                'order' => 0,
            ],
            [ //Building View
                'name' => 'building_view',
                'display_value' => 'Building View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'building,view',
                'order' => 0,
            ],
            [ //Cable TV
                'name' => 'cable_tv',
                'display_value' => 'Cable TV',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'cable,tv',
                'order' => 0,
            ],
            [ //Carpet Area (Max Sq Ft)
                'name' => 'max_carpet_area_sft',
                'display_value' => 'Carpet Area (Max Sq Ft)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'carpet,area,sft',
                'order' => 0,
            ],
            [ //Carpet Area (Min Sq Ft)
                'name' => 'min_carpet_area_sft',
                'display_value' => 'Carpet Area (Min Sq Ft)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'carpet,area,sft',
                'order' => 0,
            ],
            [ //Chimney
                'name' => 'chimney',
                'display_value' => 'Chimney',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'chimney',
                'order' => 0,
            ],
            [ //City View
                'name' => 'city_view',
                'display_value' => 'City View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'city,view',
                'order' => 0,
            ],
            [ //Club House
                'name' => 'club_house',
                'display_value' => 'Club House',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'club,house',
                'order' => 0,
            ],
            [ //Coffee Table
                'name' => 'coffee_table',
                'display_value' => 'Coffee Table',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'coffee,table',
                'order' => 0,
            ],
            [ //Configuration
                'name' => 'configuration',
                'display_value' => 'Configuration',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => 'configuration,bhk,bedrooms',
                'order' => 2,
                'data_json' => [
                    'name' => 'configuration',
                    'display_value' => 'Configuration',
                    'values' => [
                        ['value' => '1BHK', 'display_value' => '1BHK', 'order' => 1],
                        ['value' => '2BHK', 'display_value' => '2BHK', 'order' => 2],
                        ['value' => '3BHK', 'display_value' => '3BHK', 'order' => 3],
                        ['value' => '4BHK', 'display_value' => '4BHK', 'order' => 4],
                        ['value' => '4+BHK','display_value' => '4+BHK','order' => 5],
                    ]
                ]
            ],
            [ //Construction Status
                'name' => 'construction_status',
                'display_value' => 'Construction Status',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'construction,status',
                'order' => 0,
                'data_json' => [
                    'name' => 'construction_status',
                    'display_value' => 'Construction_Status',
                    'values' => [
                        ['value' => 'ready_to_move', 'display_value' => 'Ready To Move'],
                        ['value' => 'under_construction', 'display_value' => 'Under Construction']
                    ]
                ]
            ],
            [ //Corporate or Individual
                'name' => 'corporate_or_individual',
                'display_value' => 'Corporate or Individual',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'corporate,individual',
                'order' => 0,
                'data_json' => [
                    'name' => 'corporate_or_individual',
                    'display_value' => 'Corporate_Or_Individual',
                    'values' => [
                        ['value' => 'Corporate Lease Only', 'display_value' => 'Corporate Only'],
                        ['value' => 'Individual Lease Only', 'display_value' => 'Individual Only']
                    ]
                ]
            ],
            [ //Covered Car Parks (max)
                'name' => 'max_covered_car_parks',
                'display_value' => 'Covered Car Parks (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'covered,car,parks',
                'order' => 0,
            ],
            [ //Covered Car Parks (min)
                'name' => 'min_covered_car_parks',
                'display_value' => 'Covered Car Parks (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'covered,car,parks',
                'order' => 0,
            ],
            [ //Dining Area
                'name' => 'dining_area',
                'display_value' => 'Dining Area',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'dining,area',
                'order' => 0,
            ],
            [ //Dining Table
                'name' => 'dining_table',
                'display_value' => 'Dining Table',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'dining,table',
                'order' => 0,
            ],
            [ //Facing Direction
                'name' => 'facing_direction',
                'display_value' => 'Facing Direction',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => 'facing,direction',
                'order' => 3,
                'data_json' => [
                    'name' => 'facing_direction',
                    'display_value' => 'Facing_Direction',
                    'values' => [
                        ['value' => 'NORTH', 'display_value' => 'North'],
                        ['value' => 'SOUTH', 'display_value' => 'South'],
                        ['value' => 'EAST', 'display_value' => 'East'], 
                        ['value' => 'WEST', 'display_value' => 'West']
                    ]
                ]
            ],
            [ //Fans
                'name' => 'fans',
                'display_value' => 'Fans',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'fans',
                'order' => 11,
            ],
            [ //Floor Number (max)
                'name' => 'max_floor_number',
                'display_value' => 'Floor Number (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'floor,number',
                'order' => 0,
            ],
            [ //Floor Number (min)
                'name' => 'min_floor_number',
                'display_value' => 'Floor Number (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'floor,number',
                'order' => 0,
            ],
            [ //Football Court
                'name' => 'football_court',
                'display_value' => 'Football Court',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'football,court',
                'order' => 0,
            ],
            [ //Fridge
                'name' => 'fridge',
                'display_value' => 'Fridge',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'fridge',
                'order' => 0,
            ],
            [ //Furnishing State
                'name' => 'furnishing_state',
                'display_value' => 'Furnishing State',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => 'furnishing,state',
                'order' => 0,
                'data_json' => [
                    'name' => 'furnishing_state',
                    'display_value' => 'Furnishing_State',
                    'values' => [
                        ['value' => 'NONE', 'display_value' => 'Not Furnished'],
                        ['value' => 'SEMI', 'display_value' => 'Semi Furnished'],
                        ['value' => 'FULL', 'display_value' => 'Fully Furnished']
                    ]
                ]
            ],
            [ //Garden
                'name' => 'garden',
                'display_value' => 'Garden',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'garden',
                'order' => 0,
            ],
            [ //Garden View
                'name' => 'garden_view',
                'display_value' => 'Garden View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'garden,view',
                'order' => 0,
            ], 
            [ //Geysers (max)
                'name' => 'max_geysers',
                'display_value' => 'Geysers (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'geysers',
                'order' => 0,
            ],
            [ //Geysers (min)
                'name' => 'min_geysers',
                'display_value' => 'Geysers (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'geysers',
                'order' => 0,
            ],
            [ //Gym
                'name' => 'gym',
                'display_value' => 'Gym',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'gym',
                'order' => 7,
            ],
            [ //'Have Pets?
                'name' => 'have_pets',
                'display_value' => 'Have Pets?',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'have,pets',
                'order' => 0,
                'data_json' => [
                    'name' => 'have_pets',
                    'display_value' => 'Have_Pets',
                    'values' => [
                        ['value' => 'Pets OK', 'display_value' => 'Pets Allowed'],
                        ['value' => 'No Pets', 'display_value' => 'Pets Not Allowed']
                    ]
                ]
            ],
            [ //Indoor Games
                'name' => 'indoor_games',
                'display_value' => 'Indoor Games',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'indoor,games',
                'order' => 0,
            ],
            [ //Internet
                'name' => 'internet',
                'display_value' => 'Internet',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'internet',
                'order' => 0,
            ],
            [ //Jogging Track
                'name' => 'jogging_track',
                'display_value' => 'Jogging Track',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'jogging,track',
                'order' => 8,
            ],
            [ //Kids Play Area
                'name' => 'kids_play_area',
                'display_value' => 'Kids Play Area',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'play,area',
                'order' => 0,
            ],
            [ //Lake View
                'name' => 'lake_view',
                'display_value' => 'Lake View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'lake,view',
                'order' => 0,
            ], 
            [ //Lift
                'name' => 'lift',
                'display_value' => 'Lift',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'lift',
                'order' => 0,
            ], 
            [ //Lights
                'name' => 'lights',
                'display_value' => 'Lights',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'lights',
                'order' => 12,
            ],
            [ //Maintenance (max)
                'name' => 'max_maintenance',
                'display_value' => 'Maintenance (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'maintenance',
                'order' => 0,
            ],
            [ //Maintenance (min)
                'name' => 'min_maintenance',
                'display_value' => 'Maintenance (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'maintenance',
                'order' => 0,
            ],
            [ //Microwave
                'name' => 'microwave',
                'display_value' => 'Microwave',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'microwave',
                'order' => 0,
            ],
            [ //Modular Kitchen
                'name' => 'modular_kitchen',
                'display_value' => 'Modular Kitchen',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'modular,kitchen',
                'order' => 0,
            ],
            [ //Monthly Rent (max)
                'name' => 'max_monthly_rent',
                'display_value' => 'Monthly Rent (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'monthly,rent',
                'order' => 0,
            ],
            [ //Monthly Rent (min)
                'name' => 'min_monthly_rent',
                'display_value' => 'Monthly Rent (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'monthly,rent',
                'order' => 0,
            ],
            [ //Mountain View
                'name' => 'mountain_view',
                'display_value' => 'Mountain View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'mountain,view',
                'order' => 0,
            ],
            [ //Ocean View
                'name' => 'ocean_view',
                'display_value' => 'Ocean View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'ocean,view',
                'order' => 0,
            ],
            [ //Open Car Parks (max)
                'name' => 'max_open_car_parks',
                'display_value' => 'Open Car Parks (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'open,car,parks',
                'order' => 0,
            ],
            [ //Open Car Parks (min)
                'name' => 'min_open_car_parks',
                'display_value' => 'Open Car Parks (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'open,car,parks',
                'order' => 0,
            ],
            [ //Other Charges (max)
                'name' => 'max_other_charges',
                'display_value' => 'Other Charges (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'other,charges',
                'order' => 0,
            ],
            [ //Other Charges (min)
                'name' => 'min_other_charges',
                'display_value' => 'Other Charges (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'other,charges',
                'order' => 0,
            ],
            [ //Oven
                'name' => 'oven',
                'display_value' => 'Oven',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'oven',
                'order' => 0,
            ],
            [ //Park View
                'name' => 'park_view',
                'display_value' => 'Park View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'park,view',
                'order' => 0,
            ],
            [ //Piped Gas
                'name' => 'piped_gas',
                'display_value' => 'Piped Gas',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'piped,gas',
                'order' => 13,
            ],
            [ //Pooja Spcace
                'name' => 'pooja_spcace',
                'display_value' => 'Pooja Spcace',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'pooja',
                'order' => 0,
            ],
            [ //Pool View
                'name' => 'pool_view',
                'display_value' => 'Pool View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'pool,view',
                'order' => 0,
            ],
            [ //Power Backup
                'name' => 'power_backup',
                'display_value' => 'Power Backup',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'power,backup',
                'order' => 10,
            ],
            [ //Property Category
                'name' => 'property_category',
                'display_value' => 'Property Category',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'keywords' => 'property,category',
                'order' => 0,
                'data_json' => [
                    'name' => 'property_category',
                    'display_value' => 'Property_Category',
                    'values' => [
                        ['value' => 'commercial', 'display_value' => 'Commercial'],
                        ['value' => 'residential', 'display_value' => 'Residential']
                    ]
                ]
            ],
            [ //Property Type
                'name' => 'property_type',
                'display_value' => 'Property Type',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => 'property,type',
                'order' => 0,
                'data_json' => [
                    'name' => 'property_type',
                    'display_value' => 'Property_Type',
                    'values' => [
                        ['value' => 'Apartment', 'display_value' => 'Apartment'],
                        ['value' => 'Independent_house', 'display_value' => 'Independent House'],
                        ['value' => 'Penthouse', 'display_value' => 'Pent House'],
                        ['value' => 'Row House', 'display_value' => 'Row House'],
                        ['value' => 'Villa', 'display_value' => 'Villa']
                    ]
                ]
            ],
            [ //Sale Price (max)
                'name' => 'max_sale_price',
                'display_value' => 'Sale Price (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'sale,price',
                'order' => 6,
            ],
            [ //Sale Price (min)
                'name' => 'min_sale_price',
                'display_value' => 'Sale Price (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'sale,price',
                'order' => 5,
            ],
            [ //Sale Source
                'name' => 'sale_source',
                'display_value' => 'Sale Source',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'sale,source',
                'order' => 0,
                'data_json' => [
                    'name' => 'sale_source',
                    'display_value' => 'Sale_Source',
                    'values' => [
                        ['value' => 'new', 'display_value' => 'New'],
                        ['value' => 'resale', 'display_value' => 'Resale']
                    ]
                ]
            ],
            [ //SB Area (Max Sq Ft)
                'name' => 'max_sb_area_sft',
                'display_value' => 'SB Area (Max Sq Ft)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'sb,area,sft',
                'order' => 0,
            ],
            [ //SB Area (Min Sq Ft)
                'name' => 'min_sb_area_sft',
                'display_value' => 'SB Area (Min Sq Ft)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'sb,area,sft',
                'order' => 0,
            ],
            [ //Security
                'name' => 'security',
                'display_value' => 'Security',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'security',
                'order' => 9,
            ],
            [ //Security Deposit (max)
                'name' => 'max_security_deposit',
                'display_value' => 'Security Deposit (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'security,deposit',
                'order' => 0,
            ],
            [ //Security System
                'name' => 'security_system',
                'display_value' => 'Security System',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'security,system',
                'order' => 0,
            ],
            [ //Servant Room
                'name' => 'servant_room',
                'display_value' => 'Servant Room',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'servant',
                'order' => 0,
            ],
            [ //Sewage Treatment
                'name' => 'sewage_treatment',
                'display_value' => 'Sewage Treatment',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'sewage,treatment',
                'order' => 0,
            ],
            [ //Sofa
                'name' => 'sofa',
                'display_value' => 'Sofa',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'sofa',
                'order' => 0,
            ],
            [ //Stove
                'name' => 'stove',
                'display_value' => 'Stove',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'stove',
                'order' => 0,
            ],
            [ //Street View
                'name' => 'street_view',
                'display_value' => 'Street View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'street,view',
                'order' => 0,
            ],
            [ //Swimming Pool
                'name' => 'swimming_pool',
                'display_value' => 'Swimming Pool',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'swimming,pool',
                'order' => 0,
            ],
            [ //Tennis Court
                'name' => 'tennis_court',
                'display_value' => 'Tennis Court',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'tennis,court',
                'order' => 0,
            ],
            [ //Total Car Parks (max)
                'name' => 'max_total_car_parks',
                'display_value' => 'Total Car Parks (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'car,parking',
                'order' => 0,
            ],
            [ //Total Car Parks (min)
                'name' => 'min_total_car_parks',
                'display_value' => 'Total Car Parks (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'car,parking',
                'order' => 0,
            ],
            [ //Total Price x Registration (max)
                'name' => 'max_total_price_x_registration',
                'display_value' => 'Total Price x Registration (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'price,cost,registration',
                'order' => 0,
            ],
            [ //Total Price Registration (min)
                'name' => 'min_total_price_registration',
                'display_value' => 'Total Price Registration (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'price,cost,registration',
                'order' => 0,
            ],
            [ //Total Rent Maint (max)
                'name' => 'max_total_rent_maint',
                'display_value' => 'Total Rent Maint (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'rent,maintenance',
                'order' => 0,
            ],
            [ //Total Rent Maint (min)
                'name' => 'min_total_rent_maint',
                'display_value' => 'Total Rent Maint (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'rent,maintenance',
                'order' => 0,
            ],
            [ //TV
                'name' => 'tv',
                'display_value' => 'TV',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'tv',
                'order' => 0,
            ],
            [ //TV Unit
                'name' => 'tv_unit',
                'display_value' => 'TV Unit',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'tv',
                'order' => 0,
            ],
            [ //Veg or Non-Veg
                'name' => 'veg_or_non-veg',
                'display_value' => 'Veg or Non-Veg',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'veg,non-veg,nonveg',
                'order' => 0,
                'data_json' => [
                    'name' => 'veg_or_non-veg',
                    'display_value' => 'Veg_Or_Non-Veg',
                    'values' => [
                        ['value' => 'Vegetarians Only', 'display_value' => 'Only Veg'],
                        ['value' => 'Non-Vegetarians OK', 'display_value' => 'Non Veg Allowed']
                    ]
                ]
            ],
            [ //Washing Machine
                'name' => 'washing_machine',
                'display_value' => 'Washing Machine',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'washing,machine',
                'order' => 14,
            ],
            [ //Water Purifier
                'name' => 'water_purifier',
                'display_value' => 'Water Purifier',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'water,purifier',
                'order' => 15,
            ],
            [ //Location
                'name' => 'location',
                'display_value' => 'Location',
                'type' => ['value' => 'data_type_location'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 1,
            ],
            [ //Project
                'name' => 'project_data',
                'display_value' => 'Project',
                'type' => ['value' => 'data_type_external'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 4,
            ],
            [ //Flooring
                'name' => 'flooring',
                'display_value' => 'Flooring',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => 'flooring,tiles',
                'order' => 0,
                'data_json' => [
                    'name' => 'flooring',
                    'display_value' => 'Flooring',
                    'values' => [
                        ['value' => 'flooring_tile', 'display_value' => 'Tile'],
                        ['value' => 'flooring_carpet', 'display_value' => 'Carpet'],
                        ['value' => 'flooring_concrete', 'display_value' => 'Concrete'], 
                        ['value' => 'flooring_hardwood', 'display_value' => 'Hardwood'],
                        ['value' => 'flooring_vitrified', 'display_value' => 'Vitrified Tiles'],
                        ['value' => 'flooring_marble', 'display_value' => 'Marble Flooring'],
                        ['value' => 'flooring_granite', 'display_value' => 'Granite Flooring'], 
                        ['value' => 'flooring_ceramic', 'display_value' => 'Ceramic Tiles'],
                        ['value' => 'flooring_wood', 'display_value' => 'Wood Flooring'],
                        ['value' => 'flooring_stone', 'display_value' => 'Stone Flooring']
                    ]
                ]
            ], 
            [ //Tags
                'name' => 'tags',
                'display_value' => 'Tags',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 0,
                'data_json' => [
                    'name' => 'tags',
                    'display_value' => 'Tags',
                    'values' => [
                        ['value' => 'tags_investment', 'display_value' => 'Investment'],
                        ['value' => 'tags_own_use', 'display_value' => 'Own Use']
                    ]
                ]
            ]
        ];
        return $data;    
    } //Function ends


    /**
     * Preferences Meta Data - Travel & Tourisum
     */
    private function dataMetaPreferencesTravel()
    {
        $data = [];
        $data['industry_key'] = 'industry_type_travel';
        $data['data'] = [
            [ //Travel Price (max)
                'name' => 'max_travel_price',
                'display_value' => 'Travel Price (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'travel,price,estimate',
                'order' => 6,
            ],
            [ //Travel Price (min)
                'name' => 'min_travel_price',
                'display_value' => 'Travel Price (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'travel,price,estimate',
                'order' => 5,
            ],
            [ //Location
                'name' => 'travel_location',
                'display_value' => 'Location',
                'type' => ['value' => 'data_type_location'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 1,
            ],
            [ //Group Size (max)
                'name' => 'maxgroup_size',
                'display_value' => 'Group Size (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'group,size',
                'order' => 6,
            ],
            [ //Group Size (min)
                'name' => 'min_group_size',
                'display_value' => 'Group Size (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'group,size',
                'order' => 5,
            ],
            [ //Tour Manager
                'name' => 'tour_manager',
                'display_value' => 'Tour Manager',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'manager,tour-manager',
                'order' => 0,
            ],
            [ //Types
                'name' => 'travel_type',
                'display_value' => 'Type',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 0,
                'data_json' => [
                    'name' => 'travel_types',
                    'display_value' => 'Types',
                    'values' => [
                        ['value' => 'travel_type_adventure', 'display_value' => 'Adventure'],
                        ['value' => 'travel_type_business', 'display_value' => 'Business'],
                        ['value' => 'travel_type_weekend', 'display_value' => 'Weekend']
                    ]
                ]
            ]
        ];
        return $data; 
    } //Function ends


    /**
     * Preferences Meta Data - Retail
     */
    private function dataMetaPreferencesRetail()
    {
        return  [
        ];
    } //Function ends
}