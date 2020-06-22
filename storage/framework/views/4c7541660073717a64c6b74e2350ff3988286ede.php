<?php $__env->startSection('site_title', formatTitle([__('Plans'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Plans')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="d-flex">
    <div class="flex-grow-1">
        <h2 class="mb-3 d-inline-block"><?php echo e(__('Plans')); ?></h2>
    </div>
    <div>
        <?php if(config('settings.stripe')): ?>
            <a href="<?php echo e(route('admin.plans.new')); ?>" class="btn btn-primary mb-3"><?php echo e(__('New')); ?></a>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col"><div class="font-weight-medium py-1"><?php echo e(__('Plans')); ?></div></div>
            <div class="col-auto">
                <form method="GET" action="<?php echo e(route('admin.plans')); ?>">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control" name="search" value="<?php echo e(app('request')->input('search')); ?>" placeholder="<?php echo e(__('Search')); ?>">
                        <div class="input-group-append">
                            <button type="button" class="btn <?php echo e(request()->input('sort') ? 'btn-primary' : 'btn-outline-primary'); ?> d-flex align-items-center dropdown-toggle dropdown-toggle-split reset-after" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $__env->make('icons.filter', ['class' => 'fill-current icon-button-sm'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>&#8203;</button>
                            <div class="dropdown-menu <?php echo e((__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right')); ?> border-0 shadow" id="search-filters">
                                <div class="dropdown-header py-1">
                                    <div class="row">
                                        <div class="col"><div class="font-weight-medium m-0 text-dark"><?php echo e(__('Filters')); ?></div></div>
                                        <div class="col-auto">
                                            <?php if(request()->input('sort')): ?>
                                                <a href="<?php echo e(route('admin.plans')); ?>" class="text-secondary"><?php echo e(__('Reset')); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <div class="form-group px-4">
                                    <label for="i_visibility" class="small"><?php echo e(__('Visibility')); ?></label>
                                    <select name="visibility" id="i_visibility" class="custom-select custom-select-sm">
                                        <option value=""><?php echo e(__('All')); ?></option>
                                        <?php $__currentLoopData = [1 => __('Public'), 0 => __('Private')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php if(request()->input('public') == $key && request()->input('public') !== null): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="i_status" class="small"><?php echo e(__('Status')); ?></label>
                                    <select name="status" id="i_status" class="custom-select custom-select-sm">
                                        <option value=""><?php echo e(__('All')); ?></option>
                                        <?php $__currentLoopData = [0 => __('Active'), 1 => __('Disabled')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php if(request()->input('disabled') == $key && request()->input('disabled') !== null): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="i_sort" class="small"><?php echo e(__('Sort')); ?></label>
                                    <select name="sort" id="i_sort" class="custom-select custom-select-sm">
                                        <?php $__currentLoopData = ['desc' => __('Descending'), 'asc' => __('Ascending')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php if(request()->input('sort') == $key): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="form-group px-4 mb-2">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block"><?php echo e(__('Search')); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php echo $__env->make('shared.message', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php if(count($plans) == 0): ?>
            <?php echo e(__('No results found.')); ?>

        <?php else: ?>
            <div class="list-group list-group-flush my-n3">
                <div class="list-group-item px-0 text-muted">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="row">
                                <div class="col-12 col-lg-5"><?php echo e(__('Name')); ?></div>
                                <div class="col-12 col-lg-5"><?php echo e(__('Visibility')); ?></div>
                                <div class="col-12 col-lg-2"><?php echo e(__('Status')); ?></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="btn btn-outline-primary btn-sm invisible"><?php echo e(__('Edit')); ?></div>
                        </div>
                    </div>
                </div>

                <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="row">
                                    <div class="col-12 col-lg-5">
                                        <a href="<?php echo e(route('admin.plans.edit', $plan->id)); ?>"><?php echo e($plan->name); ?></a>
                                        <?php if($plan->amount_month == 0 && $plan->amount_year == 0): ?>
                                            <span class="badge badge-secondary"><?php echo e(__('Default')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-12 col-lg-5"><span class="badge badge-<?php echo e(($plan->visibility ? 'success' : 'secondary')); ?>"><?php echo e(($plan->visibility ? __('Public') : __('Private'))); ?></span></div>
                                    <div class="col-12 col-lg-2"><span class="badge badge-<?php echo e(($plan->trashed() ? 'danger' : 'success')); ?>"><?php echo e(($plan->trashed() ? __('Disabled') : __('Active'))); ?></span></div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="<?php echo e(route('admin.plans.edit', $plan->id)); ?>" class="btn btn-outline-primary btn-sm"><?php echo e(__('Edit')); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="mt-3 align-items-center">
                    <div class="row">
                        <div class="col">
                            <div class="mt-2 mb-3"><?php echo e(__('Showing :from-:to of :total', ['from' => $plans->firstItem(), 'to' => $plans->lastItem(), 'total' => $plans->total()])); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <?php echo e($plans->onEachSide(1)->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/plans/list.blade.php ENDPATH**/ ?>