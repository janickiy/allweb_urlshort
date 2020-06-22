<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="alert" aria-label="<?php echo e(__('Close')); ?>">
            <span aria-hidden="true" class="d-flex align-items-center"><?php echo $__env->make('icons.close', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span>
        </button>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="alert" aria-label="<?php echo e(__('Close')); ?>">
            <span aria-hidden="true" class="d-flex align-items-center"><?php echo $__env->make('icons.close', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span>
        </button>
    </div>
<?php endif; ?><?php /**PATH /var/www/site3.loc/www/resources/views/shared/message.blade.php ENDPATH**/ ?>