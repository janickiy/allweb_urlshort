<?php $__env->startSection('site_title', formatTitle([__('Settings'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<h2 class="mb-3 d-inline-block"><?php echo e(__('Settings')); ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1"><?php echo e(__('Shortener')); ?></div></div>
    <div class="card-body">

        <?php echo $__env->make('shared.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <form action="<?php echo e(route('admin.settings.shortener')); ?>" method="post" enctype="multipart/form-data">

            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="i_short_guest"><?php echo e(__('Guest shortening')); ?></label>
                <select name="short_guest" id="i_short_guest" class="custom-select">
                    <?php $__currentLoopData = [0 => __('Disabled'), 1 => __('Enabled')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(config('settings.short_guest') == $key): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="i_short_bad_words"><?php echo e(__('Bad words')); ?></label>
                <textarea name="short_bad_words" id="i_short_bad_words" class="form-control" rows="3"><?php echo e(config('settings.short_bad_words')); ?></textarea>
                <small class="form-text form-text text-muted w-100"><?php echo e(__('New row acts as a delimiter.')); ?></small>
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
        </form>

    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/settings/shortener.blade.php ENDPATH**/ ?>