<?php
    $configData = Helper::appClasses();
?>

<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="<?php echo e($configData['style']); ?>-style"
    data-theme="<?php echo e($configData['theme']); ?>" data-assets-path="<?php echo e(asset('/assets') . '/'); ?>"
    data-base-url="<?php echo e(url('/')); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- admin seo integration. If a category is displayed check if the category has a seo title and description. If not, use the category name and description. -->
    <?php if(isset($category)): ?>
        <?php if($category->meta_title): ?>
            <title><?php echo e($category->meta_title); ?></title>
        <?php else: ?>
            <title>Inlando | <?php echo e($category->name); ?> mieten</title>
        <?php endif; ?>
        <?php if($category->meta_description): ?>
            <meta name="description" content="<?php echo e($category->meta_description); ?>">
        <?php else: ?>
            <meta name="description" content="<?php echo e($category->description); ?>">
        <?php endif; ?>
    <?php else: ?>
        <title><?php echo $__env->yieldContent('title', config('app.name', 'Inlando')); ?> | <?php echo e(config('variables.templateName')); ?></title>
    <?php endif; ?>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/img/favicon/favicon.ico')); ?>" />

    <!-- Template Variables -->
    <script>
        window.templateName = '<?php echo e(config('variables.templateName', 'inlando')); ?>';
    </script>
    <!-- Include Core Styles -->
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/app.css',
        'resources/assets/vendor/fonts/fontawesome.scss',
        'resources/assets/vendor/fonts/tabler-icons.scss',
        'resources/assets/vendor/fonts/flag-icons.scss',
    // 'resources/assets/vendor/css/rtl/core.scss',
        'resources/assets/vendor/scss/rtl/theme-default.scss',
        'resources/assets/vendor/scss/demo.scss',
        'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
        'resources/assets/vendor/libs/node-waves/node-waves.scss',
        'resources/assets/vendor/libs/daterangepicker/daterangepicker.scss',
        'resources/assets/vendor/scss/pages/front-page.scss'
    ]); ?>

    <?php echo $__env->yieldPushContent('page-styles'); ?>
    <?php echo $__env->yieldContent('styles'); ?>
</head>

<body>
    <!-- Navbar -->
    <?php echo $__env->make('layouts.frontend.sections.navbar.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Content -->
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <?php echo $__env->make('layouts.frontend.sections.footer.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Core JS -->
    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/assets/vendor/libs/jquery/jquery.js',
        'resources/assets/vendor/libs/popper/popper.js',
        'resources/assets/vendor/js/bootstrap.js',
        'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
        'resources/assets/vendor/libs/node-waves/node-waves.js',
        'resources/assets/vendor/libs/hammer/hammer.js',
        'resources/assets/vendor/js/helpers.js',
        'resources/assets/vendor/js/menu.js',
        'resources/assets/vendor/libs/moment/moment.js',
        'resources/assets/vendor/libs/daterangepicker/daterangepicker.js',
        'resources/assets/js/template-init.js'
    ]); ?>

    <!-- App JS - load last -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/js/front-main.js']); ?>

    <?php echo $__env->yieldPushContent('page-scripts'); ?>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html><?php /**PATH /Users/g470/Sites/platform_architecture_big/development/admin-laravel/resources/views/layouts/frontend/app.blade.php ENDPATH**/ ?>