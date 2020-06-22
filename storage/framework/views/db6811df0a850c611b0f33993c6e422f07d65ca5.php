<?php $__env->startSection('site_title', formatTitle([__('New'), __('Language'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['url' => route('admin.languages'), 'title' => __('Languages')],
    ['title' => __('New')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<h2 class="mb-3 d-inline-block"><?php echo e(__('New')); ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1"><?php echo e(__('Language')); ?></div></div>
    <div class="card-body">
        <form action="<?php echo e(route('admin.languages.new')); ?>" method="post" enctype="multipart/form-data">

            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="i_language"><?php echo e(__('Language')); ?></label>
                <div class="custom-file">
                    <input type="file" name="language" id="i_language" class="custom-file-input<?php echo e($errors->has('language') ? ' is-invalid' : ''); ?>" accept=".json">
                    <?php if($errors->has('language')): ?>
                        <span class="invalid-feedback" role="alert">
                            <strong><?php echo e($errors->first('language')); ?></strong>
                        </span>
                    <?php endif; ?>
                    <label class="custom-file-label" for="i_language" data-browse="<?php echo e(__('Browse')); ?>"><?php echo e(__('Choose file')); ?></label>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
        </form>
    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/languages/new.blade.php ENDPATH**/ ?>