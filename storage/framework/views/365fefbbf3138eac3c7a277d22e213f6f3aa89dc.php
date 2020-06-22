<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-100" dir="<?php echo e((__('lang_dir') == 'rtl' ? 'rtl' : 'ltr')); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('site_title'); ?></title>

    <link href="<?php echo e(url('/')); ?>/uploads/brand/<?php echo e(config('settings.favicon') ?? 'favicon.png'); ?>" rel="icon">

    <!-- Scripts -->
    <script src="<?php echo e(asset('js/app.js')); ?>" defer></script>

    <!-- Styles -->
    <link href="https://rsms.me/inter/inter.css" rel="stylesheet">
    <link href="<?php echo e(asset('css/app'. (__('lang_dir') == 'rtl' ? '.rtl' : '') . (config('settings.dark_mode') == 1 ? '.dark' : '').'.css')); ?>" rel="stylesheet" id="app-css">
</head>
<?php echo $__env->yieldContent('body'); ?>
</html>
<?php /**PATH /var/www/site3.loc/www/resources/views/layouts/wrapper.blade.php ENDPATH**/ ?>