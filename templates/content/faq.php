<?php
$faqs = [
    ['Do you offer free inspections?', 'Yes. We provide free inspections and estimates for most pest problems. Call us or use the form on this page to schedule one.'],
    ['Are your treatments safe for children and pets?', 'We follow an Integrated Pest Management approach and apply products according to their labels. Your technician will explain any precautions, such as how long to keep children and pets away from a treated area.'],
    ['How long does a treatment take?', 'Most routine residential services take under an hour. Termite, bed bug and wildlife work varies with the size of the property and the extent of the problem; we give you a time estimate up front.'],
    ['Do I need to leave my home during treatment?', 'For most services, no. Some treatments, such as certain bed bug or fumigation services, require you to be away for a period of time. We will tell you in advance.'],
    ['Do you service businesses?', 'Yes. We work with restaurants, offices, warehouses, apartment complexes, condominium associations and other commercial properties.'],
    ['What areas do you serve?', 'New Jersey, the five boroughs of New York City and South Florida.'],
];
?>
<?php foreach ($faqs as [$q, $a]): ?>
    <details class="faq"><summary><?= e($q) ?></summary><p><?= e($a) ?></p></details>
<?php endforeach; ?>
<?= faq_schema($faqs) ?>
