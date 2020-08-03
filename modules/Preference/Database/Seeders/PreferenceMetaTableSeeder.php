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
                        'key'               => $preference['key'],
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


            //Save configurations
            // if (!empty($organization['configurations'])) 
            // {
            //     $response->configurations()->attach($organization['configurations']);
            // } //End if            
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
                'key' => 'max_estimated_price',
                'display_value' => 'Estimated Price (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'sale,price',
                'order' => 6,
            ],
            [ //Estimated Price (min)
                'key' => 'min_estimated_price',
                'display_value' => 'Estimated Price (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'sale,price',
                'order' => 5,
            ],
            [ //Location
                'key' => 'lead_location',
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
                'key' => 'max_age_of_property',
                'display_value' => 'Age of Property (Max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'age,property,years,yrs',
                'order' => 0,
            ],
            [ //Age of Property (Min)
                'key' => 'min_age_of_property',
                'display_value' => 'Age of Property (Min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'age,property,years,yrs',
                'order' => 0,
            ],
            [ //Air Conditioners (max)
                'key' => 'max_air_conditioners',
                'display_value' => 'Air Conditioners (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'air,conditioners',
                'order' => 0,
            ],
            [ //Air Conditioners (min)
                'key' => 'min_air_conditioners',
                'display_value' => 'Air Conditioners (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'air,conditioners',
                'order' => 0
            ], 
            [ //Bachelor or Family
                'key' => 'bachelor_or_family',
                'display_value' => 'Bachelor or Family',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'family',
                'order' => 0,
                'data_json' => [
                    'key' => 'bachelor_or_family',
                    'display_value' => 'Bachelor_or_Family',
                    'values' => [
                        ['value' => 'Bachelors OK', 'display_value' => 'Bachelor'],
                        ['value' => 'Families Only', 'display_value' => 'Family Only']
                    ]
                ]
            ],
            [ //Badminton Court
                'key' => 'badminton_court',
                'display_value' => 'Badminton Court',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'badminton,court',
                'order' => 0,
            ],
            [ //Balconies (max)
                'key' => 'max_balconies',
                'display_value' => 'Balconies (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'balconies',
                'order' => 0,
            ],
            [ //Balconies (min)
                'key' => 'min_balconies',
                'display_value' => 'Balconies (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'balconies',
                'order' => 0,
            ],
            [ //Basketball Court
                'key' => 'basketball_court',
                'display_value' => 'Basketball Court',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'basketball,court',
                'order' => 0,
            ],
            [ //Bathrooms (max)
                'key' => 'max_bathrooms',
                'display_value' => 'Bathrooms (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'bathrooms',
                'order' => 0,
            ],
            [ //Bathrooms (min)
                'key' => 'min_bathrooms',
                'display_value' => 'Bathrooms (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'bathrooms',
                'order' => 0,
            ],
            [ //Beds (max)
                'key' => 'max_beds',
                'display_value' => 'Beds (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'beds',
                'order' => 0,
            ],
            [ //Beds (min)
                'key' => 'min_beds',
                'display_value' => 'Beds (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'beds',
                'order' => 0,
            ],
            [ //Building View
                'key' => 'building_view',
                'display_value' => 'Building View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'building,view',
                'order' => 0,
            ],
            [ //Cable TV
                'key' => 'cable_tv',
                'display_value' => 'Cable TV',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'cable,tv',
                'order' => 0,
            ],
            [ //Carpet Area (Max Sq Ft)
                'key' => 'max_carpet_area_sft',
                'display_value' => 'Carpet Area (Max Sq Ft)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'carpet,area,sft',
                'order' => 0,
            ],
            [ //Carpet Area (Min Sq Ft)
                'key' => 'min_carpet_area_sft',
                'display_value' => 'Carpet Area (Min Sq Ft)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'carpet,area,sft',
                'order' => 0,
            ],
            [ //Chimney
                'key' => 'chimney',
                'display_value' => 'Chimney',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'chimney',
                'order' => 0,
            ],
            [ //City View
                'key' => 'city_view',
                'display_value' => 'City View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'city,view',
                'order' => 0,
            ],
            [ //Club House
                'key' => 'club_house',
                'display_value' => 'Club House',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'club,house',
                'order' => 0,
            ],
            [ //Coffee Table
                'key' => 'coffee_table',
                'display_value' => 'Coffee Table',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'coffee,table',
                'order' => 0,
            ],
            [ //Configuration
                'key' => 'configuration',
                'display_value' => 'Configuration',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => 'configuration,bhk,bedrooms',
                'order' => 2,
                'data_json' => [
                    'key' => 'configuration',
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
                'key' => 'construction_status',
                'display_value' => 'Construction Status',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'construction,status',
                'order' => 0,
                'data_json' => [
                    'key' => 'construction_status',
                    'display_value' => 'Construction_Status',
                    'values' => [
                        ['value' => 'ready_to_move', 'display_value' => 'Ready To Move'],
                        ['value' => 'under_construction', 'display_value' => 'Under Construction']
                    ]
                ]
            ],
            [ //Corporate or Individual
                'key' => 'corporate_or_individual',
                'display_value' => 'Corporate or Individual',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'corporate,individual',
                'order' => 0,
                'data_json' => [
                    'key' => 'corporate_or_individual',
                    'display_value' => 'Corporate_Or_Individual',
                    'values' => [
                        ['value' => 'Corporate Lease Only', 'display_value' => 'Corporate Only'],
                        ['value' => 'Individual Lease Only', 'display_value' => 'Individual Only']
                    ]
                ]
            ],
            [ //Covered Car Parks (max)
                'key' => 'max_covered_car_parks',
                'display_value' => 'Covered Car Parks (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'covered,car,parks',
                'order' => 0,
            ],
            [ //Covered Car Parks (min)
                'key' => 'min_covered_car_parks',
                'display_value' => 'Covered Car Parks (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'covered,car,parks',
                'order' => 0,
            ],
            [ //Dining Area
                'key' => 'dining_area',
                'display_value' => 'Dining Area',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'dining,area',
                'order' => 0,
            ],
            [ //Dining Table
                'key' => 'dining_table',
                'display_value' => 'Dining Table',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'dining,table',
                'order' => 0,
            ],
            [ //Facing Direction
                'key' => 'facing_direction',
                'display_value' => 'Facing Direction',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => 'facing,direction',
                'order' => 3,
                'data_json' => [
                    'key' => 'facing_direction',
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
                'key' => 'fans',
                'display_value' => 'Fans',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'fans',
                'order' => 11,
            ],
            [ //Floor Number (max)
                'key' => 'max_floor_number',
                'display_value' => 'Floor Number (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'floor,number',
                'order' => 0,
            ],
            [ //Floor Number (min)
                'key' => 'min_floor_number',
                'display_value' => 'Floor Number (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'floor,number',
                'order' => 0,
            ],
            [ //Football Court
                'key' => 'football_court',
                'display_value' => 'Football Court',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'football,court',
                'order' => 0,
            ],
            [ //Fridge
                'key' => 'fridge',
                'display_value' => 'Fridge',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'fridge',
                'order' => 0,
            ],
            [ //Furnishing State
                'key' => 'furnishing_state',
                'display_value' => 'Furnishing State',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => 'furnishing,state',
                'order' => 0,
                'data_json' => [
                    'key' => 'furnishing_state',
                    'display_value' => 'Furnishing_State',
                    'values' => [
                        ['value' => 'NONE', 'display_value' => 'Not Furnished'],
                        ['value' => 'SEMI', 'display_value' => 'Semi Furnished'],
                        ['value' => 'FULL', 'display_value' => 'Fully Furnished']
                    ]
                ]
            ],
            [ //Garden
                'key' => 'garden',
                'display_value' => 'Garden',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'garden',
                'order' => 0,
            ],
            [ //Garden View
                'key' => 'garden_view',
                'display_value' => 'Garden View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'garden,view',
                'order' => 0,
            ], 
            [ //Geysers (max)
                'key' => 'max_geysers',
                'display_value' => 'Geysers (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'geysers',
                'order' => 0,
            ],
            [ //Geysers (min)
                'key' => 'min_geysers',
                'display_value' => 'Geysers (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'geysers',
                'order' => 0,
            ],
            [ //Gym
                'key' => 'gym',
                'display_value' => 'Gym',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'gym',
                'order' => 7,
            ],
            [ //'Have Pets?
                'key' => 'have_pets',
                'display_value' => 'Have Pets?',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'have,pets',
                'order' => 0,
                'data_json' => [
                    'key' => 'have_pets',
                    'display_value' => 'Have_Pets',
                    'values' => [
                        ['value' => 'Pets OK', 'display_value' => 'Pets Allowed'],
                        ['value' => 'No Pets', 'display_value' => 'Pets Not Allowed']
                    ]
                ]
            ],
            [ //Indoor Games
                'key' => 'indoor_games',
                'display_value' => 'Indoor Games',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'indoor,games',
                'order' => 0,
            ],
            [ //Internet
                'key' => 'internet',
                'display_value' => 'Internet',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'internet',
                'order' => 0,
            ],
            [ //Jogging Track
                'key' => 'jogging_track',
                'display_value' => 'Jogging Track',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'jogging,track',
                'order' => 8,
            ],
            [ //Kids Play Area
                'key' => 'kids_play_area',
                'display_value' => 'Kids Play Area',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'play,area',
                'order' => 0,
            ],
            [ //Lake View
                'key' => 'lake_view',
                'display_value' => 'Lake View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'lake,view',
                'order' => 0,
            ], 
            [ //Lift
                'key' => 'lift',
                'display_value' => 'Lift',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'lift',
                'order' => 0,
            ], 
            [ //Lights
                'key' => 'lights',
                'display_value' => 'Lights',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'lights',
                'order' => 12,
            ],
            [ //Maintenance (max)
                'key' => 'max_maintenance',
                'display_value' => 'Maintenance (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'maintenance',
                'order' => 0,
            ],
            [ //Maintenance (min)
                'key' => 'min_maintenance',
                'display_value' => 'Maintenance (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'maintenance',
                'order' => 0,
            ],
            [ //Microwave
                'key' => 'microwave',
                'display_value' => 'Microwave',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'microwave',
                'order' => 0,
            ],
            [ //Modular Kitchen
                'key' => 'modular_kitchen',
                'display_value' => 'Modular Kitchen',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'modular,kitchen',
                'order' => 0,
            ],
            [ //Monthly Rent (max)
                'key' => 'max_monthly_rent',
                'display_value' => 'Monthly Rent (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'monthly,rent',
                'order' => 0,
            ],
            [ //Monthly Rent (min)
                'key' => 'min_monthly_rent',
                'display_value' => 'Monthly Rent (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'monthly,rent',
                'order' => 0,
            ],
            [ //Mountain View
                'key' => 'mountain_view',
                'display_value' => 'Mountain View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'mountain,view',
                'order' => 0,
            ],
            [ //Ocean View
                'key' => 'ocean_view',
                'display_value' => 'Ocean View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'ocean,view',
                'order' => 0,
            ],
            [ //Open Car Parks (max)
                'key' => 'max_open_car_parks',
                'display_value' => 'Open Car Parks (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'open,car,parks',
                'order' => 0,
            ],
            [ //Open Car Parks (min)
                'key' => 'min_open_car_parks',
                'display_value' => 'Open Car Parks (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'open,car,parks',
                'order' => 0,
            ],
            [ //Other Charges (max)
                'key' => 'max_other_charges',
                'display_value' => 'Other Charges (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'other,charges',
                'order' => 0,
            ],
            [ //Other Charges (min)
                'key' => 'min_other_charges',
                'display_value' => 'Other Charges (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'other,charges',
                'order' => 0,
            ],
            [ //Oven
                'key' => 'oven',
                'display_value' => 'Oven',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'oven',
                'order' => 0,
            ],
            [ //Park View
                'key' => 'park_view',
                'display_value' => 'Park View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'park,view',
                'order' => 0,
            ],
            [ //Piped Gas
                'key' => 'piped_gas',
                'display_value' => 'Piped Gas',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'piped,gas',
                'order' => 13,
            ],
            [ //Pooja Spcace
                'key' => 'pooja_spcace',
                'display_value' => 'Pooja Spcace',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'pooja',
                'order' => 0,
            ],
            [ //Pool View
                'key' => 'pool_view',
                'display_value' => 'Pool View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'pool,view',
                'order' => 0,
            ],
            [ //Power Backup
                'key' => 'power_backup',
                'display_value' => 'Power Backup',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'power,backup',
                'order' => 10,
            ],
            [ //Property Category
                'key' => 'property_category',
                'display_value' => 'Property Category',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'keywords' => 'property,category',
                'order' => 0,
                'data_json' => [
                    'key' => 'property_category',
                    'display_value' => 'Property_Category',
                    'values' => [
                        ['value' => 'commercial', 'display_value' => 'Commercial'],
                        ['value' => 'residential', 'display_value' => 'Residential']
                    ]
                ]
            ],
            [ //Property Type
                'key' => 'property_type',
                'display_value' => 'Property Type',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => 'property,type',
                'order' => 0,
                'data_json' => [
                    'key' => 'property_type',
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
                'key' => 'max_sale_price',
                'display_value' => 'Sale Price (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'sale,price',
                'order' => 6,
            ],
            [ //Sale Price (min)
                'key' => 'min_sale_price',
                'display_value' => 'Sale Price (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'sale,price',
                'order' => 5,
            ],
            [ //Sale Source
                'key' => 'sale_source',
                'display_value' => 'Sale Source',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'sale,source',
                'order' => 0,
                'data_json' => [
                    'key' => 'sale_source',
                    'display_value' => 'Sale_Source',
                    'values' => [
                        ['value' => 'new', 'display_value' => 'New'],
                        ['value' => 'resale', 'display_value' => 'Resale']
                    ]
                ]
            ],
            [ //SB Area (Max Sq Ft)
                'key' => 'max_sb_area_sft',
                'display_value' => 'SB Area (Max Sq Ft)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'sb,area,sft',
                'order' => 0,
            ],
            [ //SB Area (Min Sq Ft)
                'key' => 'min_sb_area_sft',
                'display_value' => 'SB Area (Min Sq Ft)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'sb,area,sft',
                'order' => 0,
            ],
            [ //Security
                'key' => 'security',
                'display_value' => 'Security',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'security',
                'order' => 9,
            ],
            [ //Security Deposit (max)
                'key' => 'max_security_deposit',
                'display_value' => 'Security Deposit (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'security,deposit',
                'order' => 0,
            ],
            [ //Security System
                'key' => 'security_system',
                'display_value' => 'Security System',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'security,system',
                'order' => 0,
            ],
            [ //Servant Room
                'key' => 'servant_room',
                'display_value' => 'Servant Room',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'servant',
                'order' => 0,
            ],
            [ //Sewage Treatment
                'key' => 'sewage_treatment',
                'display_value' => 'Sewage Treatment',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'sewage,treatment',
                'order' => 0,
            ],
            [ //Sofa
                'key' => 'sofa',
                'display_value' => 'Sofa',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'sofa',
                'order' => 0,
            ],
            [ //Stove
                'key' => 'stove',
                'display_value' => 'Stove',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'stove',
                'order' => 0,
            ],
            [ //Street View
                'key' => 'street_view',
                'display_value' => 'Street View',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'street,view',
                'order' => 0,
            ],
            [ //Swimming Pool
                'key' => 'swimming_pool',
                'display_value' => 'Swimming Pool',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'swimming,pool',
                'order' => 0,
            ],
            [ //Tennis Court
                'key' => 'tennis_court',
                'display_value' => 'Tennis Court',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'tennis,court',
                'order' => 0,
            ],
            [ //Total Car Parks (max)
                'key' => 'max_total_car_parks',
                'display_value' => 'Total Car Parks (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'keywords' => 'car,parking',
                'order' => 0,
            ],
            [ //Total Car Parks (min)
                'key' => 'min_total_car_parks',
                'display_value' => 'Total Car Parks (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'keywords' => 'car,parking',
                'order' => 0,
            ],
            [ //Total Price x Registration (max)
                'key' => 'max_total_price_x_registration',
                'display_value' => 'Total Price x Registration (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'price,cost,registration',
                'order' => 0,
            ],
            [ //Total Price Registration (min)
                'key' => 'min_total_price_registration',
                'display_value' => 'Total Price Registration (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_sale' => 1],
                'keywords' => 'price,cost,registration',
                'order' => 0,
            ],
            [ //Total Rent Maint (max)
                'key' => 'max_total_rent_maint',
                'display_value' => 'Total Rent Maint (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'rent,maintenance',
                'order' => 0,
            ],
            [ //Total Rent Maint (min)
                'key' => 'min_total_rent_maint',
                'display_value' => 'Total Rent Maint (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'rent,maintenance',
                'order' => 0,
            ],
            [ //TV
                'key' => 'tv',
                'display_value' => 'TV',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'tv',
                'order' => 0,
            ],
            [ //TV Unit
                'key' => 'tv_unit',
                'display_value' => 'TV Unit',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'tv',
                'order' => 0,
            ],
            [ //Veg or Non-Veg
                'key' => 'veg_or_non-veg',
                'display_value' => 'Veg or Non-Veg',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 0,
                'filter_json' => ['is_rent' => 1],
                'keywords' => 'veg,non-veg,nonveg',
                'order' => 0,
                'data_json' => [
                    'key' => 'veg_or_non-veg',
                    'display_value' => 'Veg_Or_Non-Veg',
                    'values' => [
                        ['value' => 'Vegetarians Only', 'display_value' => 'Only Veg'],
                        ['value' => 'Non-Vegetarians OK', 'display_value' => 'Non Veg Allowed']
                    ]
                ]
            ],
            [ //Washing Machine
                'key' => 'washing_machine',
                'display_value' => 'Washing Machine',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'washing,machine',
                'order' => 14,
            ],
            [ //Water Purifier
                'key' => 'water_purifier',
                'display_value' => 'Water Purifier',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'water,purifier',
                'order' => 15,
            ],
            [ //Location
                'key' => 'location',
                'display_value' => 'Location',
                'type' => ['value' => 'data_type_location'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 1,
            ],
            [ //Project
                'key' => 'project_data',
                'display_value' => 'Project',
                'type' => ['value' => 'data_type_external'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 4,
            ],
            [ //Flooring
                'key' => 'flooring',
                'display_value' => 'Flooring',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => 'flooring,tiles',
                'order' => 0,
                'data_json' => [
                    'key' => 'flooring',
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
                'key' => 'tags',
                'display_value' => 'Tags',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 0,
                'data_json' => [
                    'key' => 'tags',
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
                'key' => 'max_travel_price',
                'display_value' => 'Travel Price (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'travel,price,estimate',
                'order' => 6,
            ],
            [ //Travel Price (min)
                'key' => 'min_travel_price',
                'display_value' => 'Travel Price (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'travel,price,estimate',
                'order' => 5,
            ],
            [ //Location
                'key' => 'travel_location',
                'display_value' => 'Location',
                'type' => ['value' => 'data_type_location'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 1,
            ],
            [ //Group Size (max)
                'key' => 'maxgroup_size',
                'display_value' => 'Group Size (max)',
                'type' => ['value' => 'data_type_number'],
                'is_maximum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'group,size',
                'order' => 6,
            ],
            [ //Group Size (min)
                'key' => 'min_group_size',
                'display_value' => 'Group Size (min)',
                'type' => ['value' => 'data_type_number'],
                'is_minimum' => 1,
                'is_multiple' => 0,
                'is_sale' => 1,
                'keywords' => 'group,size',
                'order' => 5,
            ],
            [ //Tour Manager
                'key' => 'tour_manager',
                'display_value' => 'Tour Manager',
                'type' => ['value' => 'data_type_boolean'],
                'is_multiple' => 0,
                'keywords' => 'manager,tour-manager',
                'order' => 0,
            ],
            [ //Tags
                'key' => 'travel_tags',
                'display_value' => 'Tags',
                'type' => ['value' => 'data_type_lookup'],
                'is_multiple' => 1,
                'keywords' => '',
                'order' => 0,
                'data_json' => [
                    'key' => 'tags',
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
     * Preferences Meta Data - Retail
     */
    private function dataMetaPreferencesRetail()
    {
        return  [
        ];
    } //Function ends
}