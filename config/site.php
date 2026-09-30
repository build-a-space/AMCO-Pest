<?php
/**
 * Site-wide settings. Edit business details here; every page, the schema.org
 * markup and the footer read from this one file.
 *
 * Values marked VERIFY came from public listings, not from amcopest.com itself.
 * Confirm them against the live site before launch.
 */
return [
    'name'        => 'Amco Pest Solutions',
    'legal_name'  => 'Amco Pest Solutions, Inc.',
    'tagline'     => 'Family-owned pest control for New Jersey, New York City & South Florida',
    'base_url'    => getenv('SITE_URL') ?: 'https://amcopest.com',

    // Keep false until launch: adds noindex to every page and blocks crawlers
    // in robots.txt so this rebuild never competes with the live site.
    'indexable'   => filter_var(getenv('SITE_INDEXABLE') ?: 'false', FILTER_VALIDATE_BOOLEAN),

    'phone'       => '(305) 698-7884',          // VERIFY – listed on public directories
    'phone_href'  => '+13056987884',
    'email'       => 'info@amcopest.com',        // VERIFY
    'address'     => [                           // VERIFY – from public listings
        'street'   => '1775 NJ-34',
        'city'     => 'Wall Township',
        'region'   => 'NJ',
        'postal'   => '07727',
        'country'  => 'US',
    ],
    'geo'         => ['lat' => 40.1668, 'lng' => -74.0925],
    'hours'       => 'Mo-Fr 08:00-17:00',       // VERIFY

    'social' => [
        'facebook' => 'https://www.facebook.com/AmcoPestServices/',
        'linkedin' => 'https://www.linkedin.com/company/amco-pest-services-inc-',
    ],

    // Where contact-form leads go. Leads are always appended to storage/leads.csv;
    // set LEAD_EMAIL to also send each one by mail().
    'lead_email'  => getenv('LEAD_EMAIL') ?: '',

    'default_og_image' => '/assets/img/og-default.svg',
];
