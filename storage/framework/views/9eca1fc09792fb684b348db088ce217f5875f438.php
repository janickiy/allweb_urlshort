<?php $__env->startSection('site_title', formatTitle([__('Settings'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<h2 class="mb-3 d-inline-block"><?php echo e(__('Settings')); ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1"><?php echo e(__('Captcha')); ?></div></div>
    <div class="card-body">

        <?php echo $__env->make('shared.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <form action="<?php echo e(route('admin.settings.captcha')); ?>" method="post" enctype="multipart/form-data">

            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="i_captcha_site_key"><?php echo e(__('reCAPTCHA site key')); ?></label>
                <input id="i_captcha_site_key" type="text" class="form-control<?php echo e($errors->has('captcha_site_key') ? ' is-invalid' : ''); ?>" name="captcha_site_key" value="<?php echo e(config('settings.captcha_site_key')); ?>">
                <?php if($errors->has('captcha_site_key')): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first('captcha_site_key')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="i_captcha_secret_key"><?php echo e(__('reCAPTCHA secret key')); ?></label>
                <input id="i_captcha_secret_key" type="password" class="form-control<?php echo e($errors->has('captcha_secret_key') ? ' is-invalid' : ''); ?>" name="captcha_secret_key" value="<?php echo e(config('settings.captcha_secret_key')); ?>">
                <?php if($errors->has('captcha_secret_key')): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first('captcha_secret_key')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="i_captcha_shorten"><?php echo e(Str::ucfirst(mb_strtolower(__(':name form', ['name' => __('Shorten')])))); ?></label>
                <select name="captcha_shorten" id="i_captcha_shorten" class="custom-select">
                    <?php $__currentLoopData = [0 => __('Disabled'), 1 => __('Enabled')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(config('settings.captcha_shorten') == $key): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="i_captcha_registration"><?php echo e(Str::ucfirst(mb_strtolower(__(':name form', ['name' => __('Registration')])))); ?></label>
                <select name="captcha_registration" id="i_captcha_registration" class="custom-select">
                    <?php $__currentLoopData = [0 => __('Disabled'), 1 => __('Enabled')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(config('settings.captcha_registration') == $key): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="i_captcha_contact"><?php echo e(Str::ucfirst(mb_strtolower(__(':name form', ['name' => __('Contact')])))); ?></label>
                <select name="captcha_contact" id="i_captcha_contact" class="custom-select">
                    <?php $__currentLoopData = [0 => __('Disabled'), 1 => __('Enabled')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(config('settings.captcha_contact') == $key): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
        </form>

    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/settings/captcha.blade.php ENDPATH**/ ?>