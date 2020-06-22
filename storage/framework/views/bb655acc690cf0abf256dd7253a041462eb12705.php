<div class="bg-base-0">
    <div class="container py-5">
        <div class="d-flex">
            <div class="row no-gutters w-100">
                <div class="d-flex col-12 col-md">
                    <div class="flex-grow-1 d-flex align-items-center">
                        <div>
                            <h2 class="font-weight-medium mb-0"><?php echo e(config('settings.title')); ?></h2>

                            <div class="text-muted mt-2">
                                <div class="d-inline-block <?php echo e((__('lang_dir') == 'rtl' ? 'ml-4' : 'mr-4')); ?>">
                                    <div class="d-flex">
                                        <div class="d-inline-flex align-items-center">
                                            <?php echo $__env->make('icons.info', ['class' => 'fill-current icon-dashboard'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>

                                        <div class="d-inline-block <?php echo e((__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')); ?>">
                                            <a href="<?php echo e(config('info.software.url')); ?>" class="text-dark text-decoration-none" target="_blank"><?php echo e(__('Version')); ?> <span class="badge badge-primary"><?php echo e(config('info.software.version')); ?></span></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-inline-block <?php echo e((__('lang_dir') == 'rtl' ? 'ml-4' : 'mr-4')); ?>">
                                    <div class="d-flex">
                                        <div class="d-inline-flex align-items-center">
                                            <?php echo $__env->make('icons.book', ['class' => 'fill-current icon-dashboard'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>

                                        <div class="d-inline-block <?php echo e((__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')); ?>">
                                            <a href="<?php echo e(str_replace('://', '://docs.', config('info.software.url'))); ?>" class="text-dark text-decoration-none" target="_blank"><?php echo e(__('Documentation')); ?></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-inline-block <?php echo e((__('lang_dir') == 'rtl' ? 'ml-4' : 'mr-4')); ?>">
                                    <div class="d-flex">
                                        <div class="d-inline-flex align-items-center">
                                            <?php echo $__env->make('icons.star', ['class' => 'fill-current icon-dashboard'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                        </div>

                                        <div class="d-inline-block <?php echo e((__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')); ?>">
                                            <a href="<?php echo e(config('info.software.url')); ?>" class="text-dark text-decoration-none" target="_blank"><?php echo e(config('info.software.name')); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-auto d-flex flex-row-reverse align-items-center"></div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/site3.loc/www/resources/views/admin/dashboard/header.blade.php ENDPATH**/ ?>