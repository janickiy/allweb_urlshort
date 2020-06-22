<?php $__env->startSection('site_title', formatTitle([__('Settings'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<h2 class="mb-3 d-inline-block"><?php echo e(__('Settings')); ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1"><?php echo e(__('General')); ?></div></div>
    <div class="card-body">

        <?php echo $__env->make('shared.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <form action="<?php echo e(route('admin.settings.general')); ?>" method="post" enctype="multipart/form-data">

            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="i_title"><?php echo e(__('Title')); ?></label>
                <input type="text" name="title" id="i_title" class="form-control" value="<?php echo e(config('settings.title')); ?>">
            </div>

            <div class="form-group">
                <label for="i_tagline"><?php echo e(__('Tagline')); ?></label>
                <input type="text" name="tagline" id="i_tagline" class="form-control" value="<?php echo e(config('settings.tagline')); ?>">
            </div>

            <div class="form-group">
                <label for="i_index"><?php echo e(__('Custom index')); ?></label>
                <input type="text" name="index" id="i_index" class="form-control<?php echo e($errors->has('index') ? ' is-invalid' : ''); ?>" value="<?php echo e(config('settings.index')); ?>">
                <?php if($errors->has('index')): ?>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><?php echo e($errors->first('index')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="i_timezone"><?php echo e(__('Timezone')); ?></label>
                <select name="timezone" id="i_timezone" class="custom-select">
                    <?php $__currentLoopData = timezone_identifiers_list(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(config('settings.timezone') == $value): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="i_tracking_code"><?php echo e(__('Tracking Code')); ?></label>
                <textarea name="tracking_code" id="i_tracking_code" class="form-control"><?php echo e(config('settings.tracking_code')); ?></textarea>
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
        </form>

    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/settings/general.blade.php ENDPATH**/ ?>