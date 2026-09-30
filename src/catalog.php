<?php
/**
 * Structured data the templates draw on: services, pests, counties, towns and
 * wildlife species. Page copy generated from this data is original placeholder
 * text; once real pages are imported (scripts/crawl.mjs) the imported body wins.
 */

/** Main service pages: slug => [name, short blurb, related pest-library slug] */
const SERVICES = [
    'termite-control'   => ['Termite Control', 'Inspections, Sentricon® colony elimination and Termidor® liquid treatments that protect the structure of your home.', 'pest-library-termites'],
    'rodent-control'    => ['Rodent Control', 'Mouse and rat inspections, exclusion work, trapping and monitoring for homes and businesses.', 'pest-library-rodents'],
    'bed-bug-control'   => ['Bed Bug Control', 'Discreet inspections and proven treatments that eliminate bed bugs from homes, apartments and hotels.', 'pest-library-bedbugs'],
    'wildlife-control'  => ['Wildlife Control', 'Humane removal and exclusion of raccoons, squirrels, skunks, opossums, bats and other nuisance wildlife.', 'pest-library-wildlife'],
    'mosquito-control'  => ['Mosquito Control', 'Seasonal yard treatments that cut down mosquito populations so you can enjoy the outdoors.', 'pest-library-mosquitoes'],
    'tick-control'      => ['Tick Control', 'Perimeter and yard treatments that reduce tick activity and the risk of tick-borne disease.', 'pest-library-ticks'],
    'flea-and-tick'     => ['Flea & Tick Control', 'Indoor and outdoor treatments that break the flea life cycle and keep ticks off your property.', 'pest-library-fleas'],
    'ant-control'       => ['Ant Control', 'Targeted treatments for carpenter ants, pavement ants and other species that invade kitchens and structures.', 'pest-library-ants'],
    'cockroach-control' => ['Cockroach Control', 'Baiting, crack-and-crevice treatments and sanitation guidance to eliminate cockroach infestations.', 'pest-library-cockroaches'],
    'spider-control'    => ['Spider Control', 'Interior and exterior treatments plus web removal to keep spiders out of living spaces.', 'pest-library-spiders'],
    'stinging-insect'   => ['Stinging Insect Control', 'Safe removal of wasp, hornet and yellow jacket nests from eaves, yards and wall voids.', 'pest-library-stinging-insects'],
    'bird-control'      => ['Bird Control', 'Netting, spikes and deterrents that keep pigeons and other birds off buildings.', 'pest-library-birds'],
    'bats'              => ['Bat Removal', 'Humane bat exclusion and sealing so bats leave your attic and cannot return.', 'pest-library-bats'],
    'beetles'           => ['Beetle Control', 'Identification and treatment of carpet beetles, pantry beetles and wood-boring beetles.', 'pest-library-beetles'],
    'house-flies'       => ['Fly Control', 'Sanitation guidance, fly lights and treatments for house flies and fruit flies.', 'pest-library-house-fly'],
    'muskrat'           => ['Muskrat Control', 'Trapping and habitat changes to stop muskrats damaging ponds, docks and shorelines.', 'pest-library-muskrats'],
    'spotted-lantern-fly' => ['Spotted Lanternfly Control', 'Treatments that protect trees and landscaping from invasive spotted lanternflies.', 'pest-library-spotted-lanternfly'],
];

/** Other service pages that are not pest-specific. */
const OTHER_SERVICES = [
    'insulation-encapsulation-service'           => ['Insulation & Encapsulation', 'Attic and crawlspace insulation and encapsulation that also blocks pest entry and controls moisture.'],
    'tap-insulation'                             => ['TAP® Pest Control Insulation', 'Thermal Acoustical Pest control insulation: energy savings plus built-in, long-term insect control.'],
    'disinfection-services'                      => ['Disinfection Services', 'Professional disinfection of homes, offices and commercial facilities.'],
    'power-washing'                              => ['Power Washing', 'Exterior cleaning of siding, decks, walkways and commercial surfaces.'],
    'apartment-complexes-condominium-associations' => ['Apartment & Condo Associations', 'Scheduled programs for multi-unit buildings, property managers and HOAs.'],
    'residential-services-real-estate-inspections' => ['Real Estate (WDI) Inspections', 'Wood-destroying insect inspections and reports for home buyers, sellers and agents.'],
    'commercial'                                 => ['Commercial Pest Control', 'Integrated pest management for restaurants, warehouses, offices, healthcare and property managers.'],
    'residential'                                => ['Residential Pest Control', 'Year-round home protection plans tailored to your property.'],
];

/** Service types used by the /service-areas-{town}-nj-{service} pages. */
const AREA_SERVICES = [
    'pest-control'         => ['Pest Control', 'services'],
    'ant-control'          => ['Ant Control', 'ant-control'],
    'bed-bug-exterminator' => ['Bed Bug Exterminator', 'bed-bug-control'],
    'rodent-control'       => ['Rodent Control', 'rodent-control'],
    'termite-control'      => ['Termite Control', 'termite-control'],
];

/** Pest library groupings (slug suffix after "pest-library-" => group). */
const PEST_GROUPS = [
    'Stinging Insects' => ['stinging-insects', 'bees', 'wasps', 'hornets', 'yellow-jackets', 'mud-daubers', 'carpenter-bees'],
    'Ants & Termites'  => ['ants', 'carpenter-ants', 'termites'],
    'Rodents'          => ['rodents', 'mice', 'rats', 'norway-rats', 'roof-rats'],
    'Wildlife'         => ['wildlife', 'squirrels', 'red-squirrels', 'chipmunks', 'raccoons', 'skunks', 'opossums', 'bats', 'birds', 'muskrats', 'beavers', 'porcupines'],
    'Spiders'          => ['spiders', 'house-spiders', 'black-widows', 'brown-recluse-spiders', 'wolf-spiders', 'orb-weavers'],
    'Biting & Blood-Feeding Pests' => ['bedbugs', 'fleas', 'ticks', 'mosquitoes', 'dust-mites', 'gnats'],
    'Occasional Invaders' => ['cockroaches', 'centipedes', 'millipedes', 'earwigs', 'silverfish', 'firebrats', 'crickets', 'pill-bugs', 'stink-bugs', 'box-elder-bugs', 'booklice', 'house-fly', 'fruit-flies', 'moths', 'beetles', 'weevils', 'ladybugs'],
    'Garden & Plant Pests' => ['spotted-lanternfly', 'aphids', 'scale-insects', 'mealybugs', 'thrips', 'spider-mites', 'leafhoppers', 'bagworms', 'grasshoppers', 'locusts', 'cicadas', 'butterflies'],
    'Beneficial & Harmless Insects' => ['dragonflies', 'mayflies', 'lacewings', 'praying-mantis'],
];

/** Display-name fixes for pest library slugs. */
const PEST_NAMES = [
    'bedbugs' => 'Bed Bugs', 'house-fly' => 'House Flies', 'praying-mantis' => 'Praying Mantises',
    'spotted-lanternfly' => 'Spotted Lanternflies', 'box-elder-bugs' => 'Boxelder Bugs',
    'yellow-jackets' => 'Yellow Jackets', 'mud-daubers' => 'Mud Daubers',
];

/** NJ counties with hub pages, plus NYC boroughs (by county) and South Florida. */
const COUNTIES = [
    'ocean-county'     => ['Ocean County', 'NJ'],
    'monmouth-county'  => ['Monmouth County', 'NJ'],
    'middlesex-county' => ['Middlesex County', 'NJ'],
    'somerset-county'  => ['Somerset County', 'NJ'],
    'union-county'     => ['Union County', 'NJ'],
    'essex-county'     => ['Essex County', 'NJ'],
    'hudson-county'    => ['Hudson County', 'NJ'],
    'bergen-county'    => ['Bergen County', 'NJ'],
    'mercer-county'    => ['Mercer County', 'NJ'],
    'burlington-county'=> ['Burlington County', 'NJ'],
    'camden-county'    => ['Camden County', 'NJ'],
    'hunterdon-county' => ['Hunterdon County', 'NJ'],
    'warren-county'    => ['Warren County', 'NJ'],
    'richmond-county'  => ['Richmond County (Staten Island)', 'NY'],
    'kings-county'     => ['Kings County (Brooklyn)', 'NY'],
    'queens-county'    => ['Queens County', 'NY'],
    'new-york-county'  => ['New York County (Manhattan)', 'NY'],
    'bronx-county'     => ['Bronx County', 'NY'],
];

const STATES = ['nj' => 'New Jersey', 'ny' => 'New York', 'fl' => 'Florida'];

/** Town display-name overrides (slug => name). */
const TOWN_NAMES = [
    'staten' => 'Staten Island', 'the-bronx' => 'The Bronx', 'secaucus-city' => 'Secaucus',
    'asbury' => 'Asbury Park', 'weehawkin' => 'Weehawken', 'avon-by-the-sea' => 'Avon-by-the-Sea',
    'bradley-garden' => 'Bradley Gardens', 'toms-river-township' => 'Toms River Township',
    'barnegat-township' => 'Barnegat Township', 'fontainebleau' => 'Fontainebleau',
    'upper-west-side' => 'Upper West Side', 'woodbrigde-township' => 'Woodbridge Township',
    'woodbridge' => 'Woodbridge', 'irvingtron' => 'Irvington', 'hamiltontownship' => 'Hamilton Township',
    'franklintownship' => 'Franklin Township', 'frankin-township' => 'Franklin Township',
    'cherry-hilly' => 'Cherry Hill', 'trenton-brick-township' => 'Brick Township',
    'bayonne-vineland' => 'Vineland', 'jersey-city-passaic' => 'Passaic',
    'old-bridge-middletown-township' => 'Middletown Township', 'north-vineland' => 'Vineland',
    'north-union-township' => 'Union Township', 'north-middletown-township' => 'Middletown Township',
    'middle-township' => 'Middletown Township',
];

/** Town slug => county hub slug (used for breadcrumbs and "nearby" links). */
const TOWN_COUNTY = [
    // Ocean
    'brick-township' => 'ocean-county', 'barnegat' => 'ocean-county', 'toms-river' => 'ocean-county',
    'bayville' => 'ocean-county', 'toms-river-township' => 'ocean-county', 'barnegat-township' => 'ocean-county',
    'robertsville' => 'monmouth-county',
    // Monmouth
    'rumson' => 'monmouth-county', 'marlboro' => 'monmouth-county', 'asbury' => 'monmouth-county',
    'matawan' => 'monmouth-county', 'howell' => 'monmouth-county', 'keansburg' => 'monmouth-county',
    'tinton-falls' => 'monmouth-county', 'keyport' => 'monmouth-county', 'lincroft' => 'monmouth-county',
    'upper-freehold' => 'monmouth-county', 'fair-haven' => 'monmouth-county', 'eatontown' => 'monmouth-county',
    'ramtown' => 'monmouth-county', 'wall-township' => 'monmouth-county', 'red-bank' => 'monmouth-county',
    'west-long-branch' => 'monmouth-county', 'long-branch' => 'monmouth-county', 'colts-neck' => 'monmouth-county',
    'union-beach' => 'monmouth-county', 'holmdel' => 'monmouth-county', 'middletown-township' => 'monmouth-county',
    // Middlesex
    'south-amboy' => 'middlesex-county', 'new-brunswick' => 'middlesex-county', 'sayreville' => 'middlesex-county',
    'fords' => 'middlesex-county', 'east-brunswick' => 'middlesex-county', 'woodbridge' => 'middlesex-county',
    'woodbridge-township' => 'middlesex-county', 'middlesex' => 'middlesex-county', 'south-river' => 'middlesex-county',
    'perth-amboy' => 'middlesex-county', 'kendall-park' => 'middlesex-county', 'colonia' => 'middlesex-county',
    'south-plainfield' => 'middlesex-county', 'old-bridge' => 'middlesex-county', 'carteret' => 'middlesex-county',
    'highland-park' => 'middlesex-county', 'avenel' => 'middlesex-county', 'iselin' => 'middlesex-county',
    'edison' => 'middlesex-county', 'metuchen' => 'middlesex-county', 'north-brunswick' => 'middlesex-county',
    'piscataway' => 'middlesex-county', 'helmetta' => 'middlesex-county', 'princeton-meadows' => 'middlesex-county',
    // Somerset
    'bridgewater' => 'somerset-county', 'raritan' => 'somerset-county', 'finderne' => 'somerset-county',
    'bound-brook' => 'somerset-county', 'watchung' => 'somerset-county', 'bernardsville' => 'somerset-county',
    'warren' => 'somerset-county', 'somerville' => 'somerset-county', 'manville' => 'somerset-county',
    'martinsville' => 'somerset-county', 'north-plainfield' => 'somerset-county', 'hillsborough' => 'somerset-county',
    'franklin-township' => 'somerset-county', 'east-franklin' => 'somerset-county', 'franklin-park' => 'somerset-county',
    // Union
    'elizabeth' => 'union-county', 'westfield' => 'union-county', 'cranford' => 'union-county',
    'union-township' => 'union-county', 'rahway' => 'union-county', 'scotch-plains' => 'union-county',
    'plainfield' => 'union-county', 'summit' => 'union-county', 'hillside' => 'union-county',
    'linden' => 'union-county', 'roselle' => 'union-county',
    // Essex
    'nutley' => 'essex-county', 'short-hills' => 'essex-county', 'montclair' => 'essex-county',
    'east-orange' => 'essex-county', 'newark' => 'essex-county', 'bloomfield' => 'essex-county',
    'maplewood' => 'essex-county', 'belleville' => 'essex-county', 'orange' => 'essex-county',
    'irvington' => 'essex-county',
    // Hudson
    'jersey-city' => 'hudson-county', 'secaucus-city' => 'hudson-county', 'bayonne' => 'hudson-county',
    'union-city' => 'hudson-county', 'north-bergen' => 'hudson-county',
    // Bergen
    'cliffside-park' => 'bergen-county', 'bergenfield' => 'bergen-county', 'wyckoff' => 'bergen-county',
    'fairview' => 'bergen-county', 'fair-lawn' => 'bergen-county', 'ridgefield-park' => 'bergen-county',
    'englewood' => 'bergen-county', 'lodi' => 'bergen-county', 'ramsey' => 'bergen-county',
    'elmwood-park' => 'bergen-county', 'edgewater' => 'bergen-county', 'paramus' => 'bergen-county',
    'palisades-park' => 'bergen-county', 'dumont' => 'bergen-county', 'tenafly' => 'bergen-county',
    'hasbrouck-heights' => 'bergen-county', 'oakland' => 'bergen-county', 'north-arlington' => 'bergen-county',
    'garfield' => 'bergen-county', 'rutherford' => 'bergen-county', 'new-milford' => 'bergen-county',
    'fort-lee' => 'bergen-county', 'hackensack' => 'bergen-county', 'teaneck' => 'bergen-county',
    // Mercer
    'princeton' => 'mercer-county', 'east-windsor' => 'mercer-county', 'hamilton-township' => 'mercer-county',
    'trenton' => 'mercer-county', 'ewing-township' => 'mercer-county', 'lawrence-township' => 'mercer-county',
    'mercerville' => 'mercer-county',
    // Burlington
    'burlington' => 'burlington-county', 'moorestown' => 'burlington-county', 'willingboro' => 'burlington-county',
    'mount-laurel' => 'burlington-county',
    // Camden
    'cherry-hill' => 'camden-county', 'voorhees' => 'camden-county', 'haddonfield' => 'camden-county',
    // Hunterdon
    'clinton' => 'hunterdon-county', 'flemington' => 'hunterdon-county', 'readington' => 'hunterdon-county',
    // Warren
    'phillipsburg' => 'warren-county', 'hackettstown' => 'warren-county',
    // New York
    'staten' => 'richmond-county', 'tottenville' => 'richmond-county',
    'queens' => 'queens-county', 'flushing' => 'queens-county', 'astoria' => 'queens-county', 'jamaica' => 'queens-county',
    'brooklyn' => 'kings-county', 'williamsburg' => 'kings-county', 'bushwick' => 'kings-county',
    'manhattan' => 'new-york-county', 'harlem' => 'new-york-county', 'upper-west-side' => 'new-york-county',
    'the-bronx' => 'bronx-county',
];

/** Wildlife species for the /animal-control-near-you-for-* pages: slug fragment => [name, group] */
const SPECIES = [
    'eastern-screech-owls' => ['Eastern Screech Owl', 'bird'],
    'eastern-screech-owl'  => ['Eastern Screech Owl', 'bird'],
    'great-horned-owl'     => ['Great Horned Owl', 'bird'],
    'barred-owl'           => ['Barred Owl', 'bird'],
    'red-tailed-hawk'      => ['Red-Tailed Hawk', 'bird'],
    'red-shouldered-hawk'  => ['Red-Shouldered Hawk', 'bird'],
    'coopers-hawk'         => ["Cooper's Hawk", 'bird'],
    'broad-winged-hawk'    => ['Broad-Winged Hawk', 'bird'],
    'northern-harrier'     => ['Northern Harrier', 'bird'],
    'peregrine-falcon'     => ['Peregrine Falcon', 'bird'],
    'osprey'               => ['Osprey', 'bird'],
    'american-crow'        => ['American Crow', 'bird'],
    'common-raven'         => ['Common Raven', 'bird'],
    'great-blue-heron'     => ['Great Blue Heron', 'bird'],
    'green-heron'          => ['Green Heron', 'bird'],
    'red-fox'              => ['Red Fox', 'mammal'],
    'gray-fox'             => ['Gray Fox', 'mammal'],
    'long-tailed-weasel'   => ['Long-Tailed Weasel', 'mammal'],
    'least-weasel'         => ['Least Weasel', 'mammal'],
    'american-mink'        => ['American Mink', 'mammal'],
    'fisher'               => ['Fisher', 'mammal'],
    'american-badger'      => ['American Badger', 'mammal'],
    'river-otter'          => ['River Otter', 'mammal'],
    'domestic-cats-feral-or-stray' => ['Feral & Stray Cat', 'mammal'],
    'bobcat'               => ['Bobcat', 'mammal'],
    'northern-raccoon'     => ['Raccoon', 'mammal'],
    'virginia-opossum'     => ['Virginia Opossum', 'mammal'],
    'striped-skunk'        => ['Striped Skunk', 'mammal'],
    'skunks-near-home'     => ['Skunk', 'mammal'],
    'roof-rats'            => ['Roof Rat', 'rodent'],
    'deer-mice'            => ['Deer Mouse', 'rodent'],
    'eastern-moles'        => ['Eastern Mole', 'mammal'],
    'star-nosed-moles'     => ['Star-Nosed Mole', 'mammal'],
    'moles-near-home'      => ['Mole', 'mammal'],
    'big-brown-bat'        => ['Big Brown Bat', 'bat'],
    'little-brown-bat'     => ['Little Brown Bat', 'bat'],
    'eastern-red-bat'      => ['Eastern Red Bat', 'bat'],
    'hoary-bat'            => ['Hoary Bat', 'bat'],
    'bats-near-home'       => ['Bat', 'bat'],
    'eastern-rat-snake'    => ['Eastern Rat Snake', 'snake'],
    'eastern-garter-snake' => ['Eastern Garter Snake', 'snake'],
    'northern-black-racer' => ['Northern Black Racer', 'snake'],
    'eastern-milksnake'    => ['Eastern Milksnake', 'snake'],
    'northern-water-snake' => ['Northern Water Snake', 'snake'],
    'eastern-hognose-snake'=> ['Eastern Hognose Snake', 'snake'],
];

/** Core pages with no special template: slug => [title, h1, description] */
const CORE_PAGES = [
    'about-us'       => ['About Us', 'About Amco Pest Solutions', 'Four generations of family-owned pest management serving New Jersey, New York City and South Florida.'],
    'control-services' => ['Control Services', 'Pest Control Services', 'Every pest control, wildlife and home-protection service Amco Pest Solutions offers.'],
    'faq'            => ['FAQ', 'Frequently Asked Questions', 'Answers to common questions about pest control treatments, safety, scheduling and pricing.'],
    'resources'      => ['Resources', 'Pest Control Resources', 'Guides, safety data sheets and helpful links from Amco Pest Solutions.'],
    'sds-labels'     => ['SDS & Labels', 'Safety Data Sheets & Product Labels', 'Safety data sheets (SDS) and product labels for the materials Amco Pest Solutions uses.'],
    'privacy-policy' => ['Privacy Policy', 'Privacy Policy', 'How Amco Pest Solutions collects, uses and protects your information.'],
    'home2'          => ['Home', 'Top Pest Control in NJ & NY', 'Amco Pest Solutions: family-owned pest control for New Jersey, New York City and South Florida.'],
    'new-york'       => ['New York', 'Pest Control in New York City', 'Pest control, termite and wildlife services across all five boroughs of New York City.'],
    'florida'        => ['Florida', 'Pest Control in South Florida', 'Pest control and termite services across Miami-Dade and Broward counties.'],
];
