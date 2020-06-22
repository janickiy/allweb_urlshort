<?php $__env->startSection('site_title', formatTitle([__('Settings'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<h2 class="mb-3 d-inline-block"><?php echo e(__('Settings')); ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1"><?php echo e(__('Payment')); ?></div></div>
    <div class="card-body">

        <?php echo $__env->make('shared.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <form action="<?php echo e(route('admin.settings.payment')); ?>" method="post" enctype="multipart/form-data">

            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="i_stripe"><?php echo e(__('Enabled')); ?></label>
                <select name="stripe" id="i_stripe" class="custom-select<?php echo e($errors->has('stripe') ? ' is-invalid' : ''); ?>">
                    <?php $__currentLoopData = [1 => __('Yes'), 0 => __('No')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if(config('settings.stripe') == $key): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($errors->has('stripe')): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first('stripe')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="i_stripe_key"><?php echo e(__('Stripe publishable key')); ?></label>
                <input type="text" name="stripe_key" id="i_stripe_key" class="form-control<?php echo e($errors->has('stripe_key') ? ' is-invalid' : ''); ?>" value="<?php echo e(config('settings.stripe_key')); ?>">
                <?php if($errors->has('stripe_key')): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first('stripe_key')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="i_stripe_secret"><?php echo e(__('Stripe secret key')); ?></label>
                <input type="password" name="stripe_secret" id="i_stripe_secret" class="form-control<?php echo e($errors->has('stripe_secret') ? ' is-invalid' : ''); ?>" value="<?php echo e(config('settings.stripe_secret')); ?>">
                <?php if($errors->has('stripe_secret')): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first('stripe_secret')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="i_stripe_wh_secret"><?php echo e(__('Stripe webhook secret key')); ?></label>
                <input type="password" name="stripe_wh_secret" id="i_stripe_wh_secret" class="form-control<?php echo e($errors->has('stripe_wh_secret') ? ' is-invalid' : ''); ?>" value="<?php echo e(config('settings.stripe_wh_secret')); ?>">
                <?php if($errors->has('stripe_wh_secret')): ?>
                    <span class="invalid-feedback" role="alert">
                        <strong><?php echo e($errors->first('stripe_wh_secret')); ?></strong>
                    </span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="i_stripe_wh_url"><?php echo e(__('Stripe webhook URL')); ?></label>
                <input type="text" name="stripe_wh_url" id="i_stripe_wh_url" class="form-control" value="<?php echo e(route('stripe.webhook')); ?>" disabled>
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
        </form>

    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/settings/payment.blade.php ENDPATH**/ ?>