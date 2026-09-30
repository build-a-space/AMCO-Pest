<?php
/**
 * Default site-wide settings. Anything saved from the admin dashboard
 * (/dashboard-4-admin-panel) is stored in storage/settings.json and overrides
 * these values, so edit here for defaults and in the dashboard day to day.
 *
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

    // Main office: Wall Township, NJ. Used site-wide.
    'phone'       => '(732) 681-8283',
    'phone_href'  => '+17326818283',
    'toll_free'   => '(888) 593-4948',
    'email'       => 'info@amcopest.com',        // VERIFY
    'address'     => [                           // Wall Township headquarters
        'street'   => '1775 State Route 34, Suite C7',
        'city'     => 'Wall Township',
        'region'   => 'NJ',
        'postal'   => '07727',
        'country'  => 'US',
    ],
    'geo'         => ['lat' => 40.1668, 'lng' => -74.0925],
    'hours'       => ['Mo-Fr 08:30-17:30', 'Sa 08:30-14:30'],   // schema.org format
    'hours_text'  => 'Mon–Fri 8:30am–5:30pm · Sat 8:30am–2:30pm',

    // Branding (uploads from the dashboard replace these paths).
    'logo'        => '/assets/img/logo.webp',
    'logo_light'  => '/assets/img/logo-light.webp',   // white-text version for the dark footer
    'favicon'     => '/assets/img/favicon.svg',
    // Red-orange buttons, navy bars and the logo's yellow, matching the current amcopest.com.
    'colors'      => ['primary' => '#d72a01', 'navy' => '#0f227e', 'accent' => '#f9d51d'],

    // Header text beside the logo.
    'header_line1' => 'Pest Control in New Jersey Pro Verified Expert',
    'header_line2' => 'For More Than 90 Years!',
    // Optional thin bar under the menu (empty = hidden).
    'announcement' => '',

    // Stats bar on the homepage – VERIFY the numbers.
    'stats' => [
        ['Expert Staff', '25+'],
        ['Years of Experience', '90+'],
        ['Customer Satisfaction', '1000+'],
        ['Passionate Employees', '50+'],
    ],

    // Monthly plans on the homepage – VERIFY prices and features.
    'plans' => [
        ['name' => 'Home Protection Plan', 'old' => '$45/month', 'price' => '$35/month', 'style' => 'orange',
         'features' => ['Year Round Protection', 'Covers 30+ Pests', 'Initial Service Interior/Exterior', '3 Additional Services at Request', 'Free Emergency Service']],
        ['name' => 'Convenience Plan', 'old' => '$65/month', 'price' => '$55/month', 'style' => 'gray',
         'features' => ['Year Round Protection', 'Covers 30+ Pests', '3 Exterior Power Sprays', 'Free Emergency Service', 'Carpenter Ant Control']],
        ['name' => 'Convenience Plan Plus', 'old' => '$85/month', 'price' => '$75/month', 'style' => 'yellow',
         'features' => ['Year Round Protection', 'Covers 30+ Pests', '3 Exterior Power Sprays', 'Carpenter Bee Control', 'Complete Termite Coverage']],
    ],

    // Third-party embeds (editable in the dashboard). The form embed replaces the
    // built-in form on every page; leave it empty to use the built-in form.
    'form_embed' => '<script id="__custom_form_widget" src="https://www.cdnstyles.com/static/custom_form_widget/v1/custom_form.widget.js" data="eyJiYWNrZ3JvdW5kQ29sb3IiOiIjZmZmZmZmIiwiYmFzZVVSTCI6Imh0dHBzOi8vZm9ybXMtcHJvZC5hcGlnYXRld2F5LmNvIiwiYm9yZGVyQ29sb3IiOiIjMDAwMDAwIiwiYm9yZGVyUmFkaXVzIjoiNXB4IiwiYm9yZGVyU3R5bGUiOiJzb2xpZCIsImJvcmRlcldpZHRoIjoiMXB4IiwiZm9ybUlkIjoiRm9ybUNvbmZpZ0lELTIxNWFkZmQ0LWQwZmMtNDk5OC1iZjNlLTNkMTUwMDA4NzVkNyIsInBhZGRpbmciOiIyMHB4IiwicHJpbWFyeUNvbG9yIjoiIzE4NzZEMiIsInByaW1hcnlGb250Q29sb3IiOiIjMDAwMDAwIiwid2lkdGgiOiIxMDAlIn0="></script>',
    'reviews_embed' => '<script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>' . "\n" . '<div class="elfsight-app-820f57af-31e7-4682-a86a-ce2ffb020e30"></div>',

    // "Why Choose Us" videos – one is picked at random on each visit.
    'pest_videos' => [
        ['Ants', 'https://www.canva.com/design/DAF-X_NLThE/-DG6cQ6iHB3xCgkZ503ckw/watch?embed', '/pest-library-ants'],
        ['Termites', 'https://www.canva.com/design/DAGFgVa1RP0/-Bbzx4KwhhZjAyUXlcrHJA/watch?embed', '/pest-library-termites'],
        ['Bees', 'https://www.canva.com/design/DAF-kmVZ36w/HX1E5d_Aqi39aY2WSgO_qA/watch?embed', '/pest-library-bees'],
    ],

    // Maintenance switch: when false, visitors get a "back soon" page (HTTP 503)
    // while a logged-in admin still sees the full site.
    'site_online'     => true,
    'offline_message' => "We're making some updates to our website. Please call us – we're still here to help!",

    // Secondary office shown on South Florida pages only.
    'florida_office' => [
        'phone'      => '(305) 698-7884',
        'phone_href' => '+13056987884',
    ],

    // Second office shown in the footer – VERIFY the street address.
    'second_office' => [
        'label'  => 'Amco Pest Solutions, Inc.',
        'street' => '',
        'city'   => 'Toms River, NJ',
        'phone'  => '(732) 341-1134',
    ],

    'social' => [
        'facebook' => 'https://www.facebook.com/AmcoPestServices/',
        'linkedin' => 'https://www.linkedin.com/company/amco-pest-services-inc-',
    ],

    // Where contact-form leads go. Leads are always appended to storage/leads.csv;
    // set LEAD_EMAIL to also send each one by mail().
    'lead_email'  => getenv('LEAD_EMAIL') ?: '',

    'default_og_image' => '/assets/img/og-default.jpg',
];
