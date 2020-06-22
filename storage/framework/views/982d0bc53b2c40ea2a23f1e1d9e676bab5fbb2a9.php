<?php $__env->startSection('site_title', formatTitle([__('Links'), config('settings.title')])); ?>

<?php echo $__env->make('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Links')],
]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="d-flex">
    <div class="flex-grow-1">
        <h2 class="mb-0 d-inline-block"><?php echo e(__('Links')); ?></h2>
    </div>
</div>

<div class="card border-0 shadow-sm mt-3">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col-12 col-md"><div class="font-weight-medium py-1"><?php echo e(__('Links')); ?></div></div>
            <div class="col-12 col-md-auto">
                <form method="GET" action="<?php echo e(route('admin.links')); ?>" class="d-md-flex">
                    <?php echo $__env->make('shared.filter_tags', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <div class="input-group input-group-sm">
                        <input class="form-control" name="search" placeholder="<?php echo e(__('Search')); ?>" value="<?php echo e(app('request')->input('search')); ?>">
                        <div class="input-group-append">
                            <button type="button" class="btn <?php echo e(request()->input('sort') ? 'btn-primary' : 'btn-outline-primary'); ?> d-flex align-items-center dropdown-toggle dropdown-toggle-split reset-after" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $__env->make('icons.filter', ['class' => 'fill-current icon-button-sm'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></button>
                            <div class="dropdown-menu <?php echo e((__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right')); ?> border-0 shadow" id="search-filters">
                                <div class="dropdown-header py-1">
                                    <div class="row">
                                        <div class="col"><div class="font-weight-medium m-0 text-dark"><?php echo e(__('Filters')); ?></div></div>
                                        <div class="col-auto">
                                            <?php if(request()->input('sort')): ?>
                                                <a href="<?php echo e(route('admin.links')); ?>" class="text-secondary"><?php echo e(__('Reset')); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <div class="form-group px-4">
                                    <label for="i_type" class="small"><?php echo e(__('Type')); ?></label>
                                    <select name="type" id="i_type" class="custom-select custom-select-sm">
                                        <?php $__currentLoopData = [0 => __('All'), 1 => __('Active'), 2 => __('Expired')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php if(request()->input('type') == $key && request()->input('type') !== null): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="i_by" class="small"><?php echo e(__('Search by')); ?></label>
                                    <select name="by" id="i_by" class="custom-select custom-select-sm">
                                        <?php $__currentLoopData = ['title' => __('Title'), 'alias' => __('Alias'), 'url' => __('URL')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>" <?php if(request()->input('by') == $key || !request()->input('by') && $key == 'name'): ?> selected <?php endif; ?>><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="form-group px-4">
                                    <label for="i_sort" class="small"><?php echo e(__('Sort')); ?></label>
                                    <select name="sort" id="i_sort" class="custom-select custom-select-sm">
                                        <?php $__currentLoopData = ['desc' => __('Old'), 'asc' => __('New'), 'max' => __('Best performing'), 'min' => __('Least performing')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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

        <?php if(count($links) == 0): ?>
            <?php echo e(__('No results found.')); ?>

        <?php else: ?>
            <div class="list-group list-group-flush my-n3">
                <div class="list-group-item px-0 text-muted">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="row align-items-center">
                                <div class="col-12 col-lg-5 d-flex">
                                    <?php echo e(__('URL')); ?>

                                </div>

                                <div class="col-12 col-lg-5 d-flex">
                                    <?php echo e(__('User')); ?>

                                </div>

                                <div class="col-12 col-lg-2 d-flex">
                                    <?php echo e(__('Clicks')); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-outline-primary btn-sm invisible"><?php echo e(__('Edit')); ?></a>
                        </div>
                    </div>
                </div>

                <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col text-truncate">
                                <div class="row">
                                    <div class="col-12 col-lg-5 d-flex">
                                        <div class="<?php echo e((__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')); ?> d-flex align-items-center"><img src="https://www.google.com/s2/favicons?domain=<?php echo e(parse_url($link->url)['host']); ?>" rel="noreferrer" class="icon-label"></div>

                                        <div class="text-truncate">
                                            <a href="<?php echo e(route('admin.links.edit', $link->id)); ?>"><?php echo e(str_replace(['http://', 'https://'], '', (isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias)))); ?></a>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-5 d-flex align-items-center">
                                        <?php if(isset($link->user)): ?>
                                            <div class="d-inline-block <?php echo e((__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')); ?> list-avatar">
                                                <img src="<?php echo e(gravatar(isset($link->user) ? $link->user->email : '', 48)); ?>" class="rounded-circle">
                                            </div>

                                            <a href="<?php echo e(route('admin.users.edit', $link->user->id)); ?>"<?php if($link->user->trashed()): ?> class="text-danger" <?php endif; ?>><?php echo e($link->user->name); ?></a>
                                        <?php else: ?>
                                            <div class="d-inline-block <?php echo e((__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')); ?> list-avatar">
                                                <img src="<?php echo e(gravatar('', 48, 'mp')); ?>" class="rounded-circle">
                                            </div>

                                            <div class="text-muted"><?php echo e(__('Guest')); ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-12 col-lg-2 d-flex">
                                        <?php if(isset($link->user)): ?>
                                            <a href="<?php echo e(route('stats', ['id' => $link->id])); ?>" class="text-dark"><?php echo e($link->clicks); ?></a>
                                        <?php else: ?>
                                            <?php echo e($link->clicks); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <a href="<?php echo e(route('admin.links.edit', $link->id)); ?>" class="btn btn-outline-primary btn-sm"><?php echo e(__('Edit')); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <div class="mt-3 align-items-center">
                    <div class="row">
                        <div class="col">
                            <div class="mt-2 mb-3"><?php echo e(__('Showing :from-:to of :total', ['from' => $links->firstItem(), 'to' => $links->lastItem(), 'total' => $links->total()])); ?>

                            </div>
                        </div>
                        <div class="col-auto">
                            <?php echo e($links->onEachSide(1)->links()); ?>

                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/admin/links/list.blade.php ENDPATH**/ ?>