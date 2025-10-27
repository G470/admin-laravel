<?php $__env->startSection('title', 'Kategorienübersicht'); ?>

<?php $__env->startSection('styles'); ?>
    <style>
        .hero-section {
            position: relative;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .hero-image {
            position: relative;
        }

        .hero-image::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .search-form {
            position: relative;
            z-index: 2;
        }

        .card-hover {
            transition: all 0.25s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .landing-hero-bg {
            background: #2222229c;
            background-size: cover;
            background-position: center;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;

        }



        /* This is just to transition when you change the viewport size. */
        * {
            transition: all 0.5s ease-out;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- 🔎 Hero-Sektion mit Suchformular -->
    <section class="section-py landing-hero-bg position-relative d-flex align-items-center justify-content-center">
        <div class="hero-image position-absolute top-0 start-0 w-100 h-100"
            style="background-size: cover; background-position: center;">
        </div>
        <div class="gradient position-absolute top-0 start-0 w-100 h-100"></div>

        <div class="container position-relative d-flex align-items-center justify-content-evenly" style="height: 500px;">
            <div class="hero-text-box text-center z-1 rounded-3 position-relative mt-n4 py-5 my-5 flex-grow-1">
                <h1 class="text-white mb-0 display-6 fw-bold"><?php echo e($heroTitle); ?></h1>
                <div class="search-form bg-white p-4 rounded-3 shadow mt-4">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('search-form');

$__html = app('livewire')->mount($__name, $__params, 'lw-2356521482-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
            </div>
        </div>
    </section>

    <!-- 🧭 Kategorien durchsuchen -->
    <?php if($categoriesSectionEnabled): ?>
        <section class="section-py">
            <div class="container">
                <h2 class="text-center mb-2 display-6"><?php echo e($categoriesSectionTitle); ?></h2>
                <p class="text-center mb-5 text-body"><?php echo e($categoriesSectionSubtitle); ?></p>
                <div class="row gy-4 mt-2">
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="<?php echo e(route('category.show', $category->slug)); ?>" class="text-decoration-none">
                                <div class="card card-hover h-100 shadow-sm border-0">
                                    <img src="<?php echo e($category->category_image ?: asset('assets/images/categories/default.svg')); ?>"
                                        class="card-img-top" alt="<?php echo e($category->name); ?>">
                                    <div class="card-body text-center">
                                        <h5 class="card-title fw-semibold text-heading"><?php echo e($category->name); ?></h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12 text-center">
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0">Keine Kategorien verfügbar.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- 🚌 Themenbereich: Wohnmobil entdecken -->
    <?php if($wohnmobilSectionEnabled): ?>
        <section class="section-py bg-body">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 text-center">
                        <h2 class="mb-2 display-6"><?php echo e($wohnmobilSectionTitle); ?></h2>
                        <p class="mb-4 text-body"><?php echo e($wohnmobilSectionSubtitle); ?></p>
                        <a href="<?php echo e($wohnmobilSectionButtonLink); ?>"
                            class="btn btn-primary btn-lg waves-effect waves-light"><?php echo e($wohnmobilSectionButtonText); ?></a>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- 🎪 Eventartikel mieten -->
    <?php if($eventsSectionEnabled): ?>
        <section class="section-py">
            <div class="container">
                <h2 class="text-center mb-2 display-6"><?php echo e($eventsSectionTitle); ?></h2>
                <p class="text-center mb-5 text-body"><?php echo e($eventsSectionSubtitle); ?></p>
                <div class="row gy-4 mt-2">
                    <?php $__empty_1 = true; $__currentLoopData = $eventItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-6 col-md-3">
                            <div class="card card-hover h-100 shadow-sm border-0">
                                <img src="<?php echo e($item->image); ?>" class="card-img-top" alt="<?php echo e($item->name); ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-semibold text-heading"><?php echo e($item->name); ?></h5>
                                    <a href="<?php echo e(route('category.show', $item->slug)); ?>"
                                        class="btn btn-outline-primary mt-2 waves-effect">Anzeigen</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12 text-center">
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0">Keine Eventartikel verfügbar.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?php echo e($eventsSectionButtonLink); ?>"
                        class="btn btn-primary waves-effect waves-light"><?php echo e($eventsSectionButtonText); ?></a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- 🚛 Nutzfahrzeuge & Freizeitfahrzeuge -->
    <?php if($vehiclesSectionEnabled): ?>
        <section class="section-py bg-body">
            <div class="container">
                <h2 class="text-center mb-2 display-6"><?php echo e($vehiclesSectionTitle); ?></h2>
                <p class="text-center mb-5 text-body"><?php echo e($vehiclesSectionSubtitle); ?></p>
                <div class="row gy-4 mt-2">
                    <?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-12 col-md-4">
                            <div class="card card-hover h-100 shadow-sm border-0">
                                <img src="<?php echo e($vehicle->image); ?>" class="card-img-top" alt="<?php echo e($vehicle->name); ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-semibold text-heading"><?php echo e($vehicle->name); ?></h5>
                                    <p class="card-text text-body"><?php echo e($vehicle->description); ?></p>
                                    <a href="<?php echo e(route('category.show', $vehicle->slug)); ?>"
                                        class="btn btn-outline-primary waves-effect">Anzeigen</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12 text-center">
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0">Keine Fahrzeuge verfügbar.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?php echo e($vehiclesSectionButtonLink); ?>"
                        class="btn btn-primary waves-effect waves-light"><?php echo e($vehiclesSectionButtonText); ?></a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- 🏗️ Baumaschinen & Bauzubehör -->
    <?php if($constructionSectionEnabled): ?>
        <section class="section-py">
            <div class="container">
                <h2 class="text-center mb-2 display-6"><?php echo e($constructionSectionTitle); ?></h2>
                <p class="text-center mb-5 text-body"><?php echo e($constructionSectionSubtitle); ?></p>
                <div class="row gy-4 mt-2">
                    <?php $__empty_1 = true; $__currentLoopData = $constructionTools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="col-12 col-md-4">
                            <div class="card card-hover h-100 shadow-sm border-0">
                                <img src="<?php echo e($tool->image); ?>" class="card-img-top" alt="<?php echo e($tool->name); ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-semibold text-heading"><?php echo e($tool->name); ?></h5>
                                    <p class="card-text text-body"><?php echo e($tool->description); ?></p>
                                    <a href="<?php echo e(route('category.show', $tool->slug)); ?>"
                                        class="btn btn-outline-primary waves-effect">Anzeigen</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-12 text-center">
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0">Keine Baumaschinen verfügbar.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?php echo e($constructionSectionButtonLink); ?>"
                        class="btn btn-primary waves-effect waves-light"><?php echo e($constructionSectionButtonText); ?></a>
                </div>
            </div>
        </section>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/contentNavbarLayoutFrontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/g470/Sites/platform_architecture_big/development/admin-laravel/resources/views/inlando/categories.blade.php ENDPATH**/ ?>