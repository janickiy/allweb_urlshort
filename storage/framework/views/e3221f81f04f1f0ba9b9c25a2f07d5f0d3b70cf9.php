<?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="input-group input-group-sm mb-3 mb-md-0 <?php echo e((__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')); ?>">
        <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroup-sizing-sm"><?php echo $__env->make('icons.'.$key, ['class' => 'fill-current icon-button-sm'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span>
        </div>
        <input type="text" class="form-control" value="<?php echo e($value); ?>" readonly>
    </div>
    <input type="hidden" class="form-control" name="<?php echo e($key); ?>_id" value="<?php echo e(request()->input($key.'_id')); ?>">
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH /var/www/site3.loc/www/resources/views/shared/filter_tags.blade.php ENDPATH**/ ?>