<?php $__env->startSection('site_title', formatTitle([__('Users'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Users')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<h2 class="mb-3 d-inline-block"><?php echo e(__('Users')); ?></h2>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col"><div class="font-weight-medium py-1"><?php echo e(__('Users')); ?></div></div>
            <div class="col-auto">
                <form method="GET" action="<?php echo e(route('admin.users')); ?>">
                    <div class="input-group input-group-sm">
                        <input class="form-control" name="search" placeholder="<?php echo e(__('Search')); ?>" value="<?php echo e(app('request')->input('search')); ?>">
                        <div class="input-group-append">
                            <button type="button" class="btn <?php echo e(request()->input('sort') ? 'btn-primary' : 'btn-outline-primary'); ?> d-flex align-items-center dropdown-toggle dropdown-toggle-split reset-after" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $__env->make('icons.filter', ['class' => 'fill-current icon-button-sm'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>&#8203;</button>
                            <div class="dropdown-menu <?php echo e((__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right')); ?> border-0 shadow" id="search-filters">
                                <div class="dropdown-header py-1">
                                    <div class="row">
                                        <div class="col"><div class="font-weight-medium m-0 text-dark"><?php echo e(__('Filters')); ?></div></div>
                                        <div class="col-auto">
                                            <?php if(request()->input('sort')): ?>
                                                <a href="<?php echo e(route('admin.users')); ?>" class="text-secondary"><?php echo e(__('Reset')); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <div class="form-group px-4">
                                    <label for="i_role" class="small"><?php echo e(__('Role')); ?></label>
                                    <select name="role" id="i_role" class="custom-select custom-select-sm">
                                        <option value=""><?php echo e(__('All')); ?></option>
                                        <?php $__currentLoopData = [0 => __('User'), 1 => __('Admin')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php if(request()->input('role') == $key && request()->input('role') !== null): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="i_by" class="small"><?php echo e(__('Search by')); ?></label>
                                    <select name="by" id="i_by" class="custom-select custom-select-sm">
                                        <?php $__currentLoopData = ['name' => __('Name'), 'email' => __('Email')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php if(request()->input('by') == $key || !request()->input('by') && $key == 'name'): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
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

        <?php if(count($users) == 0): ?>
            <?php echo e(__('No results found.')); ?>

        <?php else: ?>
            <div class="list-group list-group-flush my-n3">
                <div class="list-group-item px-0 text-muted">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="row">
                                <div class="col-12 col-lg-6"><?php echo e(__('Name')); ?></div>
                                <div class="col-12 col-lg-6"><?php echo e(__('Email')); ?></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="btn btn-outline-primary btn-sm invisible"><?php echo e(__('Edit')); ?></div>
                        </div>
                    </div>
                </div>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="row">
                                    <div class="col-12 col-lg-6 d-flex align-items-center">
                                        <div class="d-inline-block <?php echo e((__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')); ?> list-avatar">
                                            <img src="<?php echo e(gravatar($user->email, 48)); ?>" class="rounded-circle">
                                        </div>
                                        <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>"<?php if($user->trashed()): ?> class="text-danger" <?php endif; ?>><?php echo e($user->name); ?></a>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <?php echo e($user->email); ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn btn-outline-primary btn-sm"><?php echo e(__('Edit')); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="mt-3 align-items-center">
                    <div class="row">
                        <div class="col">
                            <div class="mt-2 mb-3"><?php echo e(__('Showing :from-:to of :total', ['from' => $users->firstItem(), 'to' => $users->lastItem(), 'total' => $users->total()])); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <?php echo e($users->onEachSide(1)->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/users/list.blade.php ENDPATH**/ ?>