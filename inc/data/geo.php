<?php
/**
 * NORMALISED GLOBAL GEOGRAPHIC DATABASE & TAXONOMY
 *
 * Supports multi-country administrative structures:
 * World -> Country -> State/Province/Region -> District/County -> City -> Locality/Area -> Sector/Neighborhood -> Postal/ZIP
 *
 * Designed for scalable location resolution without hardcoding thousands of static files.
 */

return [
    'countries' => [
        'IN' => [
            'code'           => 'IN',
            'name'           => 'India',
            'slug'           => 'india',
            'continent'      => 'Asia',
            'currency'       => 'INR',
            'timezone'       => 'Asia/Kolkata',
            'has_states'     => true,
            'postal_name'    => 'Pincode',
            'canonical_slug' => 'locations/india',
            'is_primary'     => true,
        ],
        'US' => [
            'code'           => 'US',
            'name'           => 'United States',
            'slug'           => 'usa',
            'continent'      => 'North America',
            'currency'       => 'USD',
            'timezone'       => 'America/New_York',
            'has_states'     => true,
            'postal_name'    => 'ZIP Code',
            'canonical_slug' => 'locations/usa',
            'is_primary'     => false,
        ],
        'GB' => [
            'code'           => 'GB',
            'name'           => 'United Kingdom',
            'slug'           => 'uk',
            'continent'      => 'Europe',
            'currency'       => 'GBP',
            'timezone'       => 'Europe/London',
            'has_states'     => false,
            'postal_name'    => 'Postcode',
            'canonical_slug' => 'locations/uk',
            'is_primary'     => false,
        ],
        'AE' => [
            'code'           => 'AE',
            'name'           => 'United Arab Emirates',
            'slug'           => 'uae',
            'continent'      => 'Asia',
            'currency'       => 'AED',
            'timezone'       => 'Asia/Dubai',
            'has_states'     => true, // Emirates
            'postal_name'    => 'PO Box',
            'canonical_slug' => 'locations/uae',
            'is_primary'     => false,
        ],
        'CA' => [
            'code'           => 'CA',
            'name'           => 'Canada',
            'slug'           => 'canada',
            'continent'      => 'North America',
            'currency'       => 'CAD',
            'timezone'       => 'America/Toronto',
            'has_states'     => true, // Provinces
            'postal_name'    => 'Postal Code',
            'canonical_slug' => 'locations/canada',
            'is_primary'     => false,
        ],
        'AU' => [
            'code'           => 'AU',
            'name'           => 'Australia',
            'slug'           => 'australia',
            'continent'      => 'Oceania',
            'currency'       => 'AUD',
            'timezone'       => 'Australia/Sydney',
            'has_states'     => true,
            'postal_name'    => 'Postcode',
            'canonical_slug' => 'locations/australia',
            'is_primary'     => false,
        ],
        'SG' => [
            'code'           => 'SG',
            'name'           => 'Singapore',
            'slug'           => 'singapore',
            'continent'      => 'Asia',
            'currency'       => 'SGD',
            'timezone'       => 'Asia/Singapore',
            'has_states'     => false,
            'postal_name'    => 'Postal Code',
            'canonical_slug' => 'locations/singapore',
            'is_primary'     => false,
        ],
        'DE' => [
            'code'           => 'DE',
            'name'           => 'Germany',
            'slug'           => 'germany',
            'continent'      => 'Europe',
            'currency'       => 'EUR',
            'timezone'       => 'Europe/Berlin',
            'has_states'     => true,
            'postal_name'    => 'PLZ',
            'canonical_slug' => 'locations/germany',
            'is_primary'     => false,
        ]
    ],

    'states' => [
        'up' => ['code' => 'UP', 'name' => 'Uttar Pradesh', 'country_code' => 'IN', 'slug' => 'uttar-pradesh'],
        'dl' => ['code' => 'DL', 'name' => 'Delhi NCR',      'country_code' => 'IN', 'slug' => 'delhi-ncr'],
        'hr' => ['code' => 'HR', 'name' => 'Haryana',        'country_code' => 'IN', 'slug' => 'haryana'],
        'mh' => ['code' => 'MH', 'name' => 'Maharashtra',    'country_code' => 'IN', 'slug' => 'maharashtra'],
        'ka' => ['code' => 'KA', 'name' => 'Karnataka',      'country_code' => 'IN', 'slug' => 'karnataka'],
        'ca' => ['code' => 'CA', 'name' => 'California',     'country_code' => 'US', 'slug' => 'california'],
        'ny' => ['code' => 'NY', 'name' => 'New York',       'country_code' => 'US', 'slug' => 'new-york'],
        'tx' => ['code' => 'TX', 'name' => 'Texas',          'country_code' => 'US', 'slug' => 'texas'],
        'eng' => ['code' => 'ENG', 'name' => 'England',      'country_code' => 'GB', 'slug' => 'england'],
        'dxb' => ['code' => 'DXB', 'name' => 'Emirate of Dubai', 'country_code' => 'AE', 'slug' => 'dubai-emirate'],
    ],

    'cities' => [
        'greater-noida' => [
            'slug'          => 'greater-noida',
            'name'          => 'Greater Noida',
            'state_code'    => 'UP',
            'country_code'  => 'IN',
            'type'          => 'hq', // Registered Physical Office
            'lat'           => 28.5355,
            'lng'           => 77.3910,
            'postal_codes'  => ['201306', '201308', '201310'],
            'localities'    => [
                'greater-noida-west' => ['name' => 'Greater Noida West', 'slug' => 'greater-noida-west', 'pincode' => '201306', 'sector' => 'Tech Zone IV'],
                'tech-zone-iv'       => ['name' => 'Tech Zone IV',       'slug' => 'tech-zone-iv',       'pincode' => '201306', 'sector' => 'Tech Zone IV']
            ]
        ],
        'noida' => [
            'slug'          => 'noida',
            'name'          => 'Noida',
            'state_code'    => 'UP',
            'country_code'  => 'IN',
            'type'          => 'onsite_hub',
            'lat'           => 28.5708,
            'lng'           => 77.3260,
            'postal_codes'  => ['201301', '201307', '201309'],
            'localities'    => [
                'sector-62' => ['name' => 'Noida Sector 62', 'slug' => 'sector-62', 'pincode' => '201309', 'sector' => 'Sector 62'],
                'sector-63' => ['name' => 'Noida Sector 63', 'slug' => 'sector-63', 'pincode' => '201307', 'sector' => 'Sector 63']
            ]
        ],
        'delhi' => [
            'slug'          => 'delhi',
            'name'          => 'Delhi',
            'state_code'    => 'DL',
            'country_code'  => 'IN',
            'type'          => 'onsite_hub',
            'lat'           => 28.6139,
            'lng'           => 77.2090,
            'postal_codes'  => ['110001', '110019', '110020'],
            'localities'    => [
                'connaught-place' => ['name' => 'Connaught Place', 'slug' => 'connaught-place', 'pincode' => '110001', 'sector' => 'Central Delhi'],
                'okhla'           => ['name' => 'Okhla Industrial Area', 'slug' => 'okhla', 'pincode' => '110020', 'sector' => 'South Delhi']
            ]
        ],
        'gurgaon' => [
            'slug'          => 'gurgaon',
            'name'          => 'Gurgaon',
            'state_code'    => 'HR',
            'country_code'  => 'IN',
            'type'          => 'onsite_hub',
            'lat'           => 28.4595,
            'lng'           => 77.0266,
            'postal_codes'  => ['122001', '122002', '122018'],
            'localities'    => [
                'cyber-city' => ['name' => 'Cyber City', 'slug' => 'cyber-city', 'pincode' => '122002', 'sector' => 'DLF Phase 2']
            ]
        ],
        'mumbai' => [
            'slug'          => 'mumbai',
            'name'          => 'Mumbai',
            'state_code'    => 'MH',
            'country_code'  => 'IN',
            'type'          => 'regional_node',
            'lat'           => 19.0760,
            'lng'           => 72.8777,
            'postal_codes'  => ['400051', '400013', '400069'],
            'localities'    => [
                'bkc'     => ['name' => 'Bandra Kurla Complex (BKC)', 'slug' => 'bkc', 'pincode' => '400051', 'sector' => 'BKC'],
                'andheri' => ['name' => 'Andheri East Tech Hub',     'slug' => 'andheri', 'pincode' => '400069', 'sector' => 'Andheri']
            ]
        ],
        'bangalore' => [
            'slug'          => 'bangalore',
            'name'          => 'Bengaluru',
            'state_code'    => 'KA',
            'country_code'  => 'IN',
            'type'          => 'regional_node',
            'lat'           => 12.9716,
            'lng'           => 77.5946,
            'postal_codes'  => ['560034', '560038', '560066'],
            'localities'    => [
                'koramangala' => ['name' => 'Koramangala', 'slug' => 'koramangala', 'pincode' => '560034', 'sector' => 'Koramangala']
            ]
        ],
        'dubai' => [
            'slug'          => 'dubai',
            'name'          => 'Dubai',
            'state_code'    => 'DXB',
            'country_code'  => 'AE',
            'type'          => 'global_node',
            'lat'           => 25.2048,
            'lng'           => 55.2708,
            'postal_codes'  => ['00000'],
            'localities'    => [
                'difc' => ['name' => 'DIFC Financial Centre', 'slug' => 'difc', 'pincode' => '00000', 'sector' => 'DIFC']
            ]
        ],
        'london' => [
            'slug'          => 'london',
            'name'          => 'London',
            'state_code'    => 'ENG',
            'country_code'  => 'GB',
            'type'          => 'global_node',
            'lat'           => 51.5074,
            'lng'           => -0.1278,
            'postal_codes'  => ['EC1A', 'WC2N', 'SW1A'],
            'localities'    => [
                'tech-city' => ['name' => 'Tech City / Shoreditch', 'slug' => 'tech-city', 'pincode' => 'EC1V', 'sector' => 'Shoreditch']
            ]
        ],
        'san-francisco' => [
            'slug'          => 'san-francisco',
            'name'          => 'San Francisco',
            'state_code'    => 'CA',
            'country_code'  => 'US',
            'type'          => 'global_node',
            'lat'           => 37.7749,
            'lng'           => -122.4194,
            'postal_codes'  => ['94105', '94107'],
            'localities'    => [
                'soma' => ['name' => 'SoMa Tech District', 'slug' => 'soma', 'zip' => '94107', 'sector' => 'SoMa']
            ]
        ],
        'new-york' => [
            'slug'          => 'new-york',
            'name'          => 'New York',
            'state_code'    => 'NY',
            'country_code'  => 'US',
            'type'          => 'global_node',
            'lat'           => 40.7128,
            'lng'           => -74.0060,
            'postal_codes'  => ['10001', '10004'],
            'localities'    => [
                'silicon-alley' => ['name' => 'Silicon Alley', 'slug' => 'silicon-alley', 'zip' => '10010', 'sector' => 'Manhattan']
            ]
        ]
    ]
];
