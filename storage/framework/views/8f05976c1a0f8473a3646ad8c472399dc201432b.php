<?php if(count(config('app.locales')) > 1): ?>
    <div class="d-block d-md-inline-flex <?php echo e((__('lang_dir') == 'rtl' ? ' mr-lg-3' : ' ml-lg-3')); ?>" data-toggle="tooltip" title="<?php echo e(__('Change language')); ?>">
        <a href="#" class="text-secondary text-decoration-none d-flex align-items-center py-1" data-toggle="modal" data-target="#changeLanguage">
            <span class="d-flex align-items-center <?php echo e((__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')); ?>"><?php echo $__env->make('icons/language', ['class' => 'icon-text fill-current'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span>
            <span class="flex-grow-1"><span class="text-muted"><?php echo e(config('app.locales')[config('app.locale')]); ?></span></span>
        </a>
    </div>

    <div class="modal fade" id="changeLanguage" tabindex="-1" role="dialog" aria-labelledby="<?php echo e(__('Change language')); ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel"><?php echo e(__('Change language')); ?></h6>
                    <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" class="d-flex align-items-center"><?php echo $__env->make('icons.close', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span>
                    </button>
                </div>
                <form action="<?php echo e(route('locale')); ?>" method="post" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="row">
                            <?php $__currentLoopData = config('app.locales'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-6">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="i_language_<?php echo e($code); ?>" name="locale" class="custom-control-input" value="<?php echo e($code); ?>" <?php if(config('app.locale') == $code): ?> checked <?php endif; ?>>
                                        <label class="custom-control-label" for="i_language_<?php echo e($code); ?>" lang="<?php echo e($code); ?>"><?php echo e($name); ?></label>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                        <button type="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?><?php /**PATH /var/www/site3.loc/www/resources/views/shared/language.blade.php ENDPATH**/ ?>