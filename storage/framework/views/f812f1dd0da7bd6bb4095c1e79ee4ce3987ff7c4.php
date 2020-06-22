<?php $__env->startSection('menu'); ?>
    <?php
        /**
         * key => [icon, title, route, [
         *  subKey => [title, route]
         * ]]
         */
        $menu = [
            'dashboard' => ['dashboard', 'Dashboard', 'admin.dashboard'],
            'settings' => ['settings', 'Settings', null, [
                'general' => ['General', 'admin.settings.general'],
                'appearance' => ['Appearance', 'admin.settings.appearance'],
                'email' => ['Email', 'admin.settings.email'],
                'social' => ['Social', 'admin.settings.social'],
                'payment' => ['Payment', 'admin.settings.payment'],
                'registration' => ['Registration', 'admin.settings.registration'],
                'legal' => ['Legal', 'admin.settings.legal'],
                'invoice' => ['Invoice', 'admin.settings.invoice'],
                'contact' => ['Contact', 'admin.settings.contact'],
                'captcha' => ['Captcha', 'admin.settings.captcha'],
                'shortener' => ['Shortener', 'admin.settings.shortener']
            ]],
            'languages' => ['language', 'Languages', 'admin.languages'],
            'plans' => ['package', 'Plans', 'admin.plans'],
            'subscriptions' => ['subscription', 'Subscriptions', 'admin.subscriptions'],
            'users' => ['users', 'Users', 'admin.users'],
            'links' => ['link', 'Links', 'admin.links'],
            'spaces' => ['space', 'Spaces', 'admin.spaces'],
            'domains' => ['domain', 'Domains', 'admin.domains'],
            'pages' => ['page', 'Pages', 'admin.pages'],
        ];
    ?>

    <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="nav-item">
            <a class="nav-link d-flex px-4 <?php if(request()->segment(2) == $key && isset($value[3]) == false): ?> active <?php endif; ?>" <?php if(isset($value[3])): ?> data-toggle="collapse" href="#subMenu-<?php echo e($key); ?>" role="button" <?php if(array_key_exists(request()->segment(3), $value[3])): ?> aria-expanded="true" <?php else: ?> aria-expanded="false" <?php endif; ?> aria-controls="collapse-<?php echo e($key); ?>" <?php else: ?> href="<?php echo e((Route::has($value[2]) ? route($value[2]) : $value[2])); ?>" <?php endif; ?>>
                <span class="sidebar-icon d-flex align-items-center"><?php echo $__env->make('icons.' . $value[0], ['class' => 'fill-current '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span>
                <span class="flex-grow-1"><?php echo e(__($value[1])); ?></span>
                <?php if(isset($value[3])): ?> <span class="ml-auto sidebar-expand"><?php echo $__env->make('icons.expand', ['class' => 'fill-current text-muted'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span> <?php endif; ?>
            </a>
        </li>

        <?php if(isset($value[3])): ?>
            <div class="collapse sub-menu <?php if(request()->segment(2) == $key): ?> show <?php endif; ?>" id="subMenu-<?php echo e($key); ?>">
                <?php $__currentLoopData = $value[3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subKey => $subValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e((Route::has($subValue[1]) ? route($subValue[1]) : $subValue[1])); ?>" class="nav-link <?php if(request()->segment(3) == $subKey): ?> active <?php endif; ?>"><?php echo e(__($subValue[0])); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?><?php /**PATH /var/www/site3.loc/www/resources/views/admin/sidebar.blade.php ENDPATH**/ ?>