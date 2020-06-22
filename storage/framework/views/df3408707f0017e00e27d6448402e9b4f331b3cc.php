<?php if(count($breadcrumbs)): ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb px-0 bg-transparent font-weight-medium mb-0">
            <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $breadcrumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!$loop->last): ?>
                    <li class="breadcrumb-item d-flex align-items-center">
                        <?php if(isset($breadcrumb['url'])): ?>
                            <a href="<?php echo e($breadcrumb['url']); ?>" class="text-muted"><?php echo e($breadcrumb['title']); ?></a>
                        <?php else: ?>
                            <div class="text-muted"><?php echo e($breadcrumb['title']); ?></div>
                        <?php endif; ?>
                        <?php echo $__env->make((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'fill-current icon-chevron mx-3'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item active text-dark"><?php echo e($breadcrumb['title']); ?></li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ol>
    </nav>
<?php endif; ?><?php /**PATH /var/www/site3.loc/www/resources/views/shared/breadcrumbs.blade.php ENDPATH**/ ?>