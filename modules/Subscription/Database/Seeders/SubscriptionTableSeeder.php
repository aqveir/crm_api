<?php

namespace Modules\Subscription\Database\Seeders;

use Illuminate\Database\Seeder;

class SubscriptionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subscriptions = $this->dataSubscriptions();

        foreach ($subscriptions as $subscription) {
            $response = factory(\Modules\Subscription\Models\Subscription::class)->create($subscription);
        } //Loop ends
    }

    private function dataSubscriptions()
    {
        return  [
            [ //Free Subscription
                'key' => 'subscription_free',
                'display_value' => 'Free',
                'description' => '',
                'created_by' => 0,
                'data_json' => [
                    'users' => 1,
                    'contacts' => 25,
                    'leads' => 50,
                    'documents' => false,
                    'price' => [
                        'currency' => 'USD',
                        'monthly' => 0,
                        'monthly_offerprice' => 0,
                        'yearly' => 0,
                        'yearly_offerprice' => 0,
                    ]
                ]
            ],
            [ //Silver-Basic Subscription
                'key' => 'subscription_basic',
                'display_value' => 'Basic',
                'description' => '',
                'created_by' => 0,
                'data_json' => [
                    'users' => 5,
                    'contacts' => 250,
                    'leads' => 500,
                    'documents' => true,
                    'price' => [
                        'currency' => 'USD',
                        'monthly' => 5,
                        'monthly_offerprice' => 5,
                        'yearly' => 60,
                        'yearly_offerprice' => 50,
                    ]
                ]
            ],
            [ //Gold-Professional Subscription
                'key' => 'subscription_professional',
                'display_value' => 'Professional',
                'description' => 'Mid range companies/agencies on growth path',
                'created_by' => 0,
                'data_json' => [
                    'users' => 10,
                    'contacts' => 1000,
                    'leads' => 5000,
                    'documents' => true,
                    'price' => [
                        'currency' => 'USD',
                        'monthly' => 10,
                        'monthly_offerprice' => 9,
                        'yearly' => 110,
                        'yearly_offerprice' => 100,
                    ]
                ]
            ],
            [ //Platinum - Ultimate Subscription
                'key' => 'subscription_ultimate',
                'display_value' => 'Ultimate',
                'description' => 'Enterprise level companies or agencies',
                'created_by' => 0,
                'data_json' => [
                    'users' => 0,
                    'contacts' => 0,
                    'leads' => 0,
                    'documents' => true,
                    'price' => [
                        'currency' => 'USD',
                        'monthly' => 0,
                        'monthly_offerprice' => null,
                        'yearly' => 0,
                        'yearly_offerprice' => null,
                    ]
                ]
            ],
        ];
    }
}
