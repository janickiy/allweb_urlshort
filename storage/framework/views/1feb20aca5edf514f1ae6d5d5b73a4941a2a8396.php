<?php $__env->startSection('site_title', formatTitle([__('Dashboard'), config('settings.title')])); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-base-1 flex-fill">
    <?php echo $__env->make('admin.dashboard.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="bg-base-1">
        <div class="container py-3 my-3">
            <h4 class="mb-0"><?php echo e(__('Overview')); ?></h4>

            <div class="row mb-5">
                <?php
                    $cards = [
                        'users' =>
                        [
                            'title' => 'Users',
                            'value' => $stats['users'],
                            'description' => 'Manage users',
                            'route' => 'admin.users',
                            'icon' => 'icons.background.users'
                        ],
                        [
                            'title' => 'Subscriptions',
                            'value' => $stats['subscriptions'],
                            'description' => 'Manage subscriptions',
                            'route' => 'admin.subscriptions',
                            'icon' => 'icons.background.subscription'
                        ],
                        [
                            'title' => 'Plans',
                            'value' => $stats['plans'],
                            'description' => 'Manage plans',
                            'route' => 'admin.plans',
                            'icon' => 'icons.background.package'
                        ],
                        [
                            'title' => 'Links',
                            'value' => $stats['links'],
                            'description' => 'Manage links',
                            'route' => 'admin.links',
                            'icon' => 'icons.background.link'
                        ],
                        [
                            'title' => 'Spaces',
                            'value' => $stats['spaces'],
                            'description' => 'Manage spaces',
                            'route' => 'admin.spaces',
                            'icon' => 'icons.background.space'
                        ],
                        [
                            'title' => 'Domains',
                            'value' => $stats['domains'],
                            'description' => 'Manage domains',
                            'route' => 'admin.domains',
                            'icon' => 'icons.background.domain'
                        ]
                    ];
                ?>

                <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 col-md-6 col-lg-4 mt-3">
                        <div class="card border-0 shadow-sm h-100 overflow-hidden">
                            <div class="card-body d-flex">
                                <div class="flex-grow-1">
                                    <div class="text-muted font-weight-medium mb-2"><?php echo e(__($card['title'])); ?></div>
                                    <div class="h1 mb-0 font-weight-normal text-wrap"><?php echo e(number_format($card['value'], 0, __('.'), __(','))); ?></div>
                                </div>

                                <div class="icon-gradient-<?php echo e($loop->index+1); ?> text-primary d-flex align-items-top">
                                    <?php echo $__env->make($card['icon'], ['class' => 'fill-current icon-card-stats'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </div>
                            </div>
                            <div class="card-footer bg-base-2 border-0">
                                <a href="<?php echo e(route($card['route'])); ?>" class="text-muted font-weight-medium d-inline-flex align-items-baseline"><?php echo e(__($card['description'])); ?><?php echo $__env->make((__('lang_dir') == 'rtl' ? 'icons.chevron_left' : 'icons.chevron_right'), ['class' => 'icon-chevron fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <h4 class="mb-0"><?php echo e(__('Recent activity')); ?></h4>
            <div class="row">
                <div class="col-12 col-xl-6 mt-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header align-items-center">
                            <div class="row">
                                <div class="col"><div class="font-weight-medium py-1"><?php echo e(__('Latest users')); ?></div></div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if(count($users) == 0): ?>
                                <?php echo e(__('No data.')); ?>

                            <?php else: ?>
                                <div class="list-group list-group-flush my-n3">
                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col text-truncate">
                                                    <div class="row align-items-center">
                                                        <div class="col-12 d-flex">
                                                            <div class="<?php echo e((__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')); ?>"><img src="<?php echo e(gravatar($user->email, 48)); ?>" class="rounded-circle icon-label"></div>
                                                            <div class="text-truncate">
                                                                <div class="d-flex">
                                                                    <div class="text-truncate">
                                                                        <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>"<?php if($user->trashed()): ?> class="text-danger" <?php endif; ?>><?php echo e($user->name); ?></a>
                                                                    </div>
                                                                </div>

                                                                <div class="text-muted text-truncate small">
                                                                    <?php echo e($user->email); ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn btn-outline-primary btn-sm"><?php echo e(__('Edit')); ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-6 mt-3">
                    <?php if(config('settings.stripe')): ?>
                        <div class="card border-0 shadow-sm">
                            <div class="card-header align-items-center">
                                <div class="row">
                                    <div class="col"><div class="font-weight-medium py-1"><?php echo e(__('Latest subscriptions')); ?></div></div>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if(count($subscriptions) == 0): ?>
                                    <?php echo e(__('No data.')); ?>

                                <?php else: ?>
                                    <div class="list-group list-group-flush my-n3">
                                        <?php $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center">
                                                    <div class="col text-truncate">
                                                        <div class="row align-items-center">
                                                            <div class="col-12 d-flex">
                                                                <div class="<?php echo e((__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')); ?>"><img src="<?php echo e(gravatar($subscription->user->email, 48)); ?>" class="rounded-circle icon-label"></div>
                                                                <div class="text-truncate">
                                                                    <div class="d-flex">
                                                                        <div class="text-truncate">
                                                                            <a href="<?php echo e(route('admin.users.edit', $subscription->user->id)); ?>"><?php echo e($subscription->user->name); ?></a>
                                                                        </div>
                                                                        <div>
                                                                            <div class="badge badge-<?php echo e(formatStripeStatus()[$subscription->stripe_status]['status']); ?> <?php echo e((__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')); ?>"><?php echo e(formatStripeStatus()[$subscription->stripe_status]['title']); ?></div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="text-dark text-truncate small">
                                                                        <a href="<?php echo e(route('admin.subscriptions.edit', $subscription->id)); ?>" class="text-secondary"><?php echo e($subscription->name); ?></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <a href="<?php echo e(route('admin.subscriptions.edit', $subscription->id)); ?>" class="btn btn-outline-primary btn-sm"><?php echo e(__('Edit')); ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card border-0 shadow-sm">
                            <div class="card-header align-items-center">
                                <div class="row">
                                    <div class="col"><div class="font-weight-medium py-1"><?php echo e(__('Latest links')); ?></div></div>
                                </div>
                            </div>

                            <div class="card-body">
                                <?php if(count($links) == 0): ?>
                                    <?php echo e(__('No data.')); ?>

                                <?php else: ?>
                                    <div class="list-group list-group-flush my-n3">
                                        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center">
                                                    <div class="col d-flex text-truncate">
                                                        <div class="<?php echo e((__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')); ?>"><img src="https://www.google.com/s2/favicons?domain=<?php echo e(parse_url($link->url)['host']); ?>" rel="noreferrer" class="icon-label"></div>

                                                        <div class="text-truncate">
                                                            <a href="<?php echo e(route('stats', $link->id)); ?>"><?php echo e(str_replace(['http://', 'https://'], '', (isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias)))); ?></a>

                                                            <div class="text-dark text-truncate small">
                                                                <span class="text-secondary cursor-help" data-toggle="tooltip-url" title="<?php echo e($link->url); ?>"><?php echo e($link->title ?? str_replace(['http://', 'https://'], '', $link->url)); ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto d-flex">
                                                        <?php echo $__env->make('shared.buttons.copy_link', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                        <?php echo $__env->make('shared.dropdowns.link', ['admin' => true, 'options' => ['dropdown' => ['button' => true, 'edit' => true, 'share' => true, 'stats' => true, 'open' => true, 'delete' => true]]], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $__env->make('shared.modals.share_link', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('shared.modals.delete_link', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('admin.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/site3.loc/www/resources/views/admin/dashboard/content.blade.php ENDPATH**/ ?>