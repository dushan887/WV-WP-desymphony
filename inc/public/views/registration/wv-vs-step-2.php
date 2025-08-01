<?php if (!defined('ABSPATH')) { exit; } ?>

<?php
if (isset($current_step) && !empty($current_step)) {
    $step_key = $current_step;
} else {
    $file = basename(__FILE__, '.php'); // e.g. "wv-exhibitor-step-4"
    $step_key = str_replace('-', '_', preg_replace('/^wv-/', '', $file));
}

$saved_data = $_SESSION["wv_reg_{$step_key}"] ?? [];
$saved_reasons = (!empty($saved_data['wv_pointsOfInterest']) && is_array($saved_data['wv_pointsOfInterest']))
    ? $saved_data['wv_pointsOfInterest']
    : [];

// Top “top” filters
$filters = [
    'WINE',
    'FOOD',
    'SPIRITS',
    'HORECA',
];

// Tags per filter (expanded)

// Wine
$wineTags = [
    'SerbianWines',
    'N.MacedonianWines',
    'AlbanianWines',
    'BalkanWines',
    'SerbianWinemakers',
    'N.MacedonianWinemakers',
    'AlbanianWinemakers',
    'BalkanWinemakers',
    'PremiumWines',
    'OrganicWines',
    'AwardedWines',
    'AutochthonousWines',
    'BoutiqueWineries',
    'FamilyWineries',
    'GarageWineries',
    'Red',
    'White',
    'Rosé',
    'Sparkling',
    'Orange',
    'Fortified',
    'Dessert',
    'Aromatized',
    'Non-Grape',
    'WineTasting',
    'Wine&FoodPairing',
    'WineIndustry',
    'WineTrading',
    'Winemaking',
    'WineMarketing',
    'WineBranding',
    'WinePackaging',
    'WineGlass',
    'WineBottle',
    'WineCork',
    'WineTourism',
    'WineEvents',
    'WineEquipment',
    'VineyardEquipment',
    'AgriculturalEquipment',
];

// Rakija (Spirits)
$rakijaTags = [
    'Rakija(Spirits)',
    'Spirits',
    'SerbianRakija',
    'N.MacedonianRakija',
    'AlbanianRakija',
    'BalkanRakija',
    'SerbianDistillers',
    'N.MacedonianDistillers',
    'AlbanianDistillers',
    'BalkanDistillers',
    'PremiumRakija',
    'OrganicRakija',
    'AwardedRakija',
    'Geo-ProtectedRakija',
    'BoutiqueDistilleries',
    'FamilyDistilleries',
    'GarageDistilleries',
    'Sljivovica/Plum',
    'Viljamovka/Pear',
    'Kajsijevaca/Apricot',
    'Malinovaca/Raspberry',
    'Dunjevaca/Quince',
    'Jabukovaca/Apple',
    'Visnjevaca/Cherry',
    'Lozovaca/Grape',
    'Orahovaca/Walnut',
    'Travarica/Herbal',
    'Medovaca/Honey',
    'RakijaTasting',
    'Rakija&FoodPairing',
    'RakijaIndustry',
    'RakijaTrading',
    'Distilling',
    'RakijaMarketing',
    'RakijaBranding',
    'RakijaPackaging',
    'DistillationEquipment',
];

// Food & Gastronomy
$foodTags = [
    'Food',
    'Gastronomy',
    'SerbianFood',
    'N.MacedonianFood',
    'AlbanianFood',
    'BalkanFood',
    'SerbianCuisine',
    'N.MacedonianCuisine',
    'AlbanianCuisine',
    'BalkanCuisine',
    'ContemporaryCuisine',
    'PremiumFood',
    'OrganicFood',
    'GourmetExperience',
    'CulinaryArts',
    'FoodIndustry',
    'FoodTrading',
    'FoodProducing',
    'FoodMarketing',
    'FoodBranding',
    'FoodPackaging',
    'GastroTourism',
    'FoodEquipment',
];

// HoReCa & Hospitality
$horecaTags = [
    'Horeca',
    'Hospitality',
    'Hotels',
    'Restaurants',
    'Cafés',
    'Catering',
    'Wine&SpiritsTrends',
    'GastroTrends',
    'FineDiningExperience',
    'AuthenticFlavors',
    'WineSelection',
    'SpiritsSelection',
    'FoodSelection',
    'MenuInnovation',
    'MenuDevelopment',
    'WineListCuration',
    'HotelIndustry',
    'HospitalityIndustry',
    'RestaurantIndustry',
    'CateringIndustry',
    'HorecaSuppliers',
    'HorecaDistributors',
    'HorecaProfessionals',
    'HorecaEquipment',
    'Chefs',
    'YoungChefs',
    'Chef-InspiredProducts',
    'ChefCompetitions',
    'ChefDemonstrations',
    'HospitalityProcurement',
    'HospitalityManagement',
    'HospitalitySolutions',
    'HospitalityEquipment',
    'FutureOfHospitality',
    'Wine&Hospitality',
    'Rakija&Hospitality',
    'Food&Hospitality',
];

// Map them together
$tagGroups = [
    'WINE'         => $wineTags,
    'FOOD'         => $foodTags,
    'SPIRITS'       => $rakijaTags,
    'HORECA'       => $horecaTags,
];


// map them together
$tagGroups = [
    'WINE'         => $wineTags,
    'FOOD'         => $foodTags,
    'SPIRITS'       => $rakijaTags,
    'HORECA'       => $horecaTags,
];

// flatten all tags & preserve order
$options = [];
foreach ( $tagGroups as $groupTags ) {
    foreach ( $groupTags as $t ) {
        if ( ! in_array( $t, $options, true ) ) {
            $options[] = $t;
        }
    }
}

// invert map: tag → its filter group
$optionToGroup = [];
foreach ( $tagGroups as $group => $tags ) {
    foreach ( $tags as $t ) {
        $optionToGroup[ $t ] = $group;
    }
}
?>

<div class="wv-step" id="<?php echo esc_attr($step_key); ?>">

    <!-- Step Header -->
    <div id="wv-step-header" class="px-16 py-16 py-lg-24 text-center">
        <h6 class="my-0 text-uppercase ls-3 fw-600">VISITING THE FAIR</h6>
    </div>

    <!-- Step Body -->
    <div id="wv-step-body" class="py-32 px-24 px-lg-64">
        <div class="d-block text-center mt-0 mb-32">
            <h2 class="text-white mt-0 mb-12 h1">Your interests</h2>
            <p class="mb-24 text-uppercase ls-3">CHOOSE MULTIPLE OPTIONS</p>
        </div>

        <div class="row g-12 justify-content-center align-items-stretch">
            <div class="col-12 my-0">
                <label class="wv-label-block d-block my-0 text-center px-32 py-16">
                    <span>What are your areas of interest?</span>
                </label>
            </div>
            <div class="col-12 my-0">
                <!-- Filters -->
                <div class="wv-filters bg-white d-flex justify-content-center gap-8 py-16 px-32 border-bottom-1">
                <ul class="list-unstyled d-flex gap-2 mb-0 fs-16 lh-1 ls-4 text-uppercase">
                    <?php
                    $first = true;
                    foreach ( $filters as $f ) : ?>
                        <li class="px-16">
                            <a
                                class="wv-filter-btn<?php if ( $first ) { echo ' active'; $first = false; } ?>"
                                data-filter="<?php echo esc_attr( $f ); ?>"
                            >
                                <?php echo esc_html( ucfirst( strtolower( $f ) ) ); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                </div>

                <!-- Tag checkboxes -->
                <div class="d-block bg-white p-32 br-8 br-t-0">
                <div class="wv-inline-checkbox-tags d-flex flex-wrap gap-8 px-48">
                    <?php
                    $firstFilter = $filters[0];
                    foreach ( $options as $opt ) :
                    $checked = in_array( $opt, $saved_reasons, true ) ? 'checked' : '';
                    $group   = $optionToGroup[ $opt ] ?? '';
                    $display = ( $group === $firstFilter ) ? 'inline-flex' : 'none';
                    ?>
                    <label class="wv-checkbox-tag"
                            data-group="<?= esc_attr( $group ); ?>"
                            style="display:<?= $display; ?>">
                        <input
                        type="checkbox"
                        name="wv_pointsOfInterest[]"
                        value="<?= esc_attr( $opt ); ?>"
                        <?= $checked; ?>>
                        <div class="fw-500 text-center px-8">
                        <?= esc_html( $opt ); ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                </div>


            </div>
        </div>
    </div>
</div>
