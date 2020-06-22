<?php $__env->startSection('site_title', formatTitle([__('Settings'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<h2 class="mb-3 d-inline-block"><?php echo e(__('Settings')); ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1"><?php echo e(__('Contact')); ?></div></div>
    <div class="card-body">

        <?php echo $__env->make('shared.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <form action="<?php echo e(route('admin.settings.contact')); ?>" method="post" enctype="multipart/form-data">

            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="i_contact_email"><?php echo e(__('Email address')); ?></label>
                <input id="i_contact_email" type="email" class="form-control<?php echo e($errors->has('contact_email') ? ' is-invalid' : ''); ?>" name="contact_email" value="<?php echo e(config('settings.contact_email')); ?>">
                <?php if($errors->has('contact_email')): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first('contact_email')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
        </form>

    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/settings/contact.blade.php ENDPATH**/ ?>