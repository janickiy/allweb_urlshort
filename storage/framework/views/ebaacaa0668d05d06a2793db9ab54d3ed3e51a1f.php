<?php $__env->startSection('site_title', formatTitle([__('Settings'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<h2 class="mb-3 d-inline-block"><?php echo e(__('Settings')); ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1"><?php echo e(__('Appearance')); ?></div></div>
    <div class="card-body">

        <?php echo $__env->make('shared.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <form action="<?php echo e(route('admin.settings.appearance')); ?>" method="post" enctype="multipart/form-data">

            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="i_logo"><?php echo e(__('Logo')); ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-1 px-2"><img src="<?php echo e(url('/')); ?>/uploads/brand/<?php echo e(config('settings.logo')); ?>" style="max-height: 1.625rem"></span>
                    </div>
                    <div class="custom-file">
                        <input type="file" name="logo" id="i_logo" class="custom-file-input<?php echo e($errors->has('logo') ? ' is-invalid' : ''); ?>" accept="jpeg,png,bmp,gif,svg,webp">
                        <label class="custom-file-label" for="i_logo" data-browse="<?php echo e(__('Browse')); ?>"><?php echo e(__('Choose file')); ?></label>
                    </div>
                </div>
                <?php if($errors->has('logo')): ?>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><?php echo e($errors->first('logo')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="i_favicon"><?php echo e(__('Favicon')); ?></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-1 px-2"><img src="<?php echo e(url('/')); ?>/uploads/brand/<?php echo e(config('settings.favicon')); ?>" style="max-height: 1.625rem;"></span>
                    </div>
                    <div class="custom-file">
                        <input type="file" name="favicon" id="i_favicon" class="custom-file-input<?php echo e($errors->has('favicon') ? ' is-invalid' : ''); ?>" accept="jpeg,png,bmp,gif,svg,webp">
                        <label class="custom-file-label" for="i_favicon" data-browse="<?php echo e(__('Browse')); ?>"><?php echo e(__('Choose file')); ?></label>
                    </div>
                </div>
                <?php if($errors->has('favicon')): ?>
                    <span class="invalid-feedback d-block" role="alert">
                        <strong><?php echo e($errors->first('favicon')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="i_theme"><?php echo e(__('Theme')); ?> (<?php echo e(__('Default')); ?>)</label>
                <select name="theme" id="i_theme" class="custom-select<?php echo e($errors->has('theme') ? ' is-invalid' : ''); ?>">
                    <?php $__currentLoopData = [0 => __('Light'), 1 => __('Dark')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(config('settings.theme') == $key): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('theme')): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first('theme')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
        </form>

    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/settings/appearance.blade.php ENDPATH**/ ?>