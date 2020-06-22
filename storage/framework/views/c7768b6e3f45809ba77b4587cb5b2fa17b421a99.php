<?php $__env->startSection('body'); ?>
    <body class="d-flex flex-column">
        <?php echo $__env->make('shared.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="d-flex flex-column flex-fill <?php if(auth()->guard()->check()): ?> content <?php endif; ?>">
            <?php echo $__env->yieldContent('content'); ?>

            <?php echo $__env->make('shared.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo config('settings.tracking_code'); ?>

        </div>
    </body>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.wrapper', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/site3.loc/www/resources/views/layouts/app.blade.php ENDPATH**/ ?>