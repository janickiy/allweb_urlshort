<?php $__env->startSection('site_title', formatTitle([__('Settings'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<h2 class="mb-3 d-inline-block"><?php echo e(__('Settings')); ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1"><?php echo e(__('Invoice')); ?></div></div>
    <div class="card-body">

        <?php echo $__env->make('shared.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <form action="<?php echo e(route('admin.settings.invoice')); ?>" method="post" enctype="multipart/form-data">

            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="i_invoice_vendor"><?php echo e(__('Vendor')); ?></label>
                <input type="text" name="invoice_vendor" id="i_invoice_vendor" class="form-control" value="<?php echo e(config('settings.invoice_vendor')); ?>">
            </div>

            <div class="form-group">
                <label for="i_invoice_address"><?php echo e(__('Address')); ?></label>
                <input type="text" name="invoice_address" id="i_invoice_address" class="form-control" value="<?php echo e(config('settings.invoice_address')); ?>">
            </div>

            <div class="form-row">
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label for="i_invoice_city"><?php echo e(__('City')); ?></label>
                        <input type="text" name="invoice_city" id="i_invoice_city" class="form-control" value="<?php echo e(config('settings.invoice_city')); ?>">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="i_invoice_state"><?php echo e(__('State')); ?></label>
                        <input type="text" name="invoice_state" id="i_invoice_state" class="form-control" value="<?php echo e(config('settings.invoice_state')); ?>">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="form-group">
                        <label for="i_invoice_postal_code"><?php echo e(__('Postal code')); ?></label>
                        <input type="text" name="invoice_postal_code" id="i_invoice_postal_code" class="form-control" value="<?php echo e(config('settings.invoice_postal_code')); ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="i_invoice_country"><?php echo e(__('Country')); ?></label>
                <select name="invoice_country" id="i_invoice_country" class="custom-select">
                    <option value="" hidden disabled selected><?php echo e(__('Country')); ?></option>
                    <?php $__currentLoopData = config('countries'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php if($key == config('settings.invoice_country')): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="form-group">
                <label for="i_invoice_phone"><?php echo e(__('Phone')); ?></label>
                <input type="text" name="invoice_phone" id="i_invoice_phone" class="form-control" value="<?php echo e(config('settings.invoice_phone')); ?>">
            </div>

            <div class="form-group">
                <label for="i_invoice_vat_number"><?php echo e(__('VAT number')); ?></label>
                <input type="text" name="invoice_vat_number" id="i_invoice_vat_number" class="form-control" value="<?php echo e(config('settings.invoice_vat_number')); ?>">
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
        </form>

    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/settings/invoice.blade.php ENDPATH**/ ?>