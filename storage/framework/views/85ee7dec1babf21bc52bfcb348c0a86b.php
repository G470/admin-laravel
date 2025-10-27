<?php if(isset($pageConfigs)): ?>
  <?php echo Helper::updatePageConfig($pageConfigs); ?>

<?php endif; ?>

<?php
  $configData = Helper::appClasses();
?>



<?php
  /* Display elements */
  $contentNavbar = ($contentNavbar ?? true);
  $container = ($container ?? 'container-xxl');

  /* Content classes */
  $containerNav = ($containerNav ?? 'container-xxl');
?>

<?php $__env->startSection('styles'); ?>
  <?php echo $__env->yieldContent('vendor-style'); ?>
  <?php echo $__env->yieldContent('page-style'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
  <?php echo $__env->yieldContent('vendor-script'); ?>
  <?php echo $__env->yieldContent('page-script'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <!-- Content wrapper -->
  <div class="content-wrapper">

    <!-- Content -->
    <div class="<?php echo e($container); ?> flex-grow-1 container-p-y">
      <?php echo $__env->yieldContent('content'); ?>
    </div>
    <!-- / Content -->
  </div>
  <!--/ Content wrapper -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/g470/Sites/platform_architecture_big/development/admin-laravel/resources/views/layouts/contentNavbarLayoutFrontend.blade.php ENDPATH**/ ?>