<?php $__env->startSection('site_title', formatTitle([__('Settings'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<h2 class="mb-3 d-inline-block"><?php echo e(__('Settings')); ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1"><?php echo e(__('Email')); ?></div></div>
    <div class="card-body">

        <?php echo $__env->make('shared.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <form action="<?php echo e(route('admin.settings.email')); ?>" method="post" enctype="multipart/form-data">

            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="i_email_driver"><?php echo e(__('Driver')); ?></label>
                <select name="email_driver" id="i_email_driver" class="custom-select">
                    <?php $__currentLoopData = ['smtp', 'log']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(config('settings.email_driver') == $value): ?> selected <?php endif; ?>><?php echo e(ucfirst($value)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="i_email_host"><?php echo e(__('Host')); ?></label>
                <input type="text" name="email_host" id="i_email_host" class="form-control" value="<?php echo e(config('settings.email_host')); ?>">
            </div>

            <div class="form-group">
                <label for="i_email_port"><?php echo e(__('Port')); ?></label>
                <input type="number" name="email_port" id="i_email_port" class="form-control" value="<?php echo e(config('settings.email_port')); ?>">
            </div>

            <div class="form-group">
                <label for="i_email_encryption"><?php echo e(__('Encryption')); ?></label>
                <select name="email_encryption" id="i_email_encryption" class="custom-select">
                    <?php $__currentLoopData = ['tls', 'ssl']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(config('settings.email_encryption') == $value): ?> selected <?php endif; ?>><?php echo e(strtoupper($value)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="i_email_address"><?php echo e(__('Email address')); ?></label>
                <input type="email" name="email_address" id="i_email_address" class="form-control" value="<?php echo e(config('settings.email_address')); ?>">
            </div>

            <div class="form-group">
                <label for="i_email_username"><?php echo e(__('Username')); ?></label>
                <input type="text" name="email_username" id="i_email_username" class="form-control" value="<?php echo e(config('settings.email_username')); ?>">
            </div>

            <div class="form-group">
                <label for="i_email_password"><?php echo e(__('Password')); ?></label>
                <input type="password" name="email_password" id="i_email_password" class="form-control" value="<?php echo e(config('settings.email_password')); ?>">
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
        </form>

    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/settings/email.blade.php ENDPATH**/ ?>