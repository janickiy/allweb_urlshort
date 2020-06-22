<?php if(auth()->guard()->guest()): ?>
<div id="header" class="header sticky-top shadow bg-base-0 z-1025">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light px-0 py-3">
            <a href="<?php echo e(route('home')); ?>" aria-label="<?php echo e(config('settings.title')); ?>" class="navbar-brand p-0">
                <div class="logo">
                    <img src="<?php echo e(url('/')); ?>/uploads/brand/<?php echo e(config('settings.logo')); ?>">
                </div>
            </a>
            <button class="navbar-toggler border-0 p-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav pt-2 p-lg-0 <?php echo e((__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto')); ?>">
                    <?php if(config('settings.stripe')): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo e(route('pricing')); ?>" role="button"><?php echo e(__('Pricing')); ?></a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo e(route('login')); ?>" role="button"><?php echo e(__('Login')); ?></a>
                    </li>

                    <?php if(config('settings.registration_registration')): ?>
                        <li class="nav-item d-flex align-items-center">
                            <a class="btn btn-outline-primary" href="<?php echo e(route('register')); ?>" role="button"><?php echo e(__('Register')); ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </div>
</div>
<?php else: ?>
<div id="header" class="header sticky-top shadow bg-base-0 z-1025 d-lg-none">
    <div class="container-fluid">
        <nav class="navbar navbar-light px-0 py-3">
            <a href="<?php echo e(route('dashboard')); ?>" aria-label="<?php echo e(config('settings.title')); ?>" class="navbar-brand p-0">
                <div class="logo">
                    <img src="<?php echo e(url('/')); ?>/uploads/brand/<?php echo e(config('settings.logo')); ?>">
                </div>
            </a>
            <button class="slide-menu-toggle navbar-toggler border-0 p-0" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>
    </div>
</div>

<nav class="slide-menu shadow bg-base-0 ct navbar navbar-light p-0 d-flex flex-column z-1025" id="slide-menu">
    <div class="sidebar-section flex-grow-1 d-flex flex-column w-100">
        <div>
            <div class="<?php echo e((__('lang_dir') == 'rtl' ? 'pr-4' : 'pl-4')); ?> py-3 d-flex align-items-center">
                <a href="<?php echo e(route('dashboard')); ?>" aria-label="<?php echo e(config('settings.title')); ?>" class="navbar-brand p-0">
                    <div class="logo">
                        <img src="<?php echo e(url('/')); ?>/uploads/brand/<?php echo e(config('settings.logo')); ?>">
                    </div>
                </a>
                <div class="close slide-menu-toggle cursor-pointer d-lg-none d-flex align-items-center <?php echo e((__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto')); ?> px-4 py-2">
                    <?php echo $__env->make('icons.close', ['class' => 'fill-current icon-close'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>

        <div class="sidebar-section flex-grow-1 overflow-auto sidebar">
            <div class="d-flex align-items-center">
                <div class="py-3 <?php echo e((__('lang_dir') == 'rtl' ? 'pr-4 pl-0' : 'pl-4 pr-0')); ?> font-weight-medium text-muted text-uppercase flex-grow-1"><?php echo e(__('Menu')); ?></div>

                <?php if(Auth::user()->role == 1): ?>
                    <?php if(request()->segment(1) == 'admin'): ?>
                        <a class="px-4 py-2 text-decoration-none text-secondary" href="<?php echo e(route('dashboard')); ?>" data-toggle="tooltip" title="<?php echo e(__('User')); ?>" role="button"><span class="d-flex align-items-center"><?php echo $__env->make('icons.user', ['class' => 'icon-text fill-current'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span></a>
                    <?php else: ?>
                        <a class="px-4 py-2 text-decoration-none text-secondary" href="<?php echo e(route('admin.dashboard')); ?>" data-toggle="tooltip" title="<?php echo e(__('Admin')); ?>" role="button"><span class="d-flex align-items-center"><?php echo $__env->make('icons.admin', ['class' => 'icon-text fill-current'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span></a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="nav flex-column">
                <?php echo $__env->yieldContent('menu'); ?>
            </div>
        </div>

        <div class="py-3 px-4">
            <div class="progress w-100 my-2 sidebar-progress">
                <div class="progress-bar" role="progressbar" style="width: <?php echo e(($userFeatures['option_links'] == 0 ? 100 : (($stats['links'] / $userFeatures['option_links']) * 100))); ?>%"></div>
            </div>

            <div class="row no-gutters">
                <div class="col d-flex align-items-center">
                    <div class="small text-muted">
                         <?php echo e(__(':number of :total links created.', ['number' => $stats['links'], 'total' => ($userFeatures['option_links'] < 0 ? '∞' : $userFeatures['option_links'])])); ?>

                    </div>
                </div>
                <div class="col-auto d-flex align-items-center <?php echo e((__('lang_dir') == 'rtl' ? 'pr-2' : 'pl-2')); ?>">
                    <a href="<?php echo e(route('pricing')); ?>" class="text-secondary" data-toggle="tooltip" data-html="true" title="<div class='mx-2 font-size-base <?php echo e((__('lang_dir') == 'rtl' ? 'text-right' : 'text-left')); ?>'><div class='row my-2'><div class='col'><?php echo e(__('Links')); ?></div><div class='col-auto'><?php echo e(__(':number of :total', ['number' => $stats['links'], 'total' => ($userFeatures['option_links'] < 0 ? '∞' : $userFeatures['option_links'])])); ?></div></div><div class='row my-2'><div class='col'><?php echo e(__('Spaces')); ?></div><div class='col-auto'><?php echo e(__(':number of :total', ['number' => $stats['spaces'], 'total' => ($userFeatures['option_spaces'] < 0 ? '∞' : $userFeatures['option_spaces'])])); ?></div></div><div class='row my-2'><div class='col'><?php echo e(__('Domains')); ?></div><div class='col-auto'><?php echo e(__(':number of :total', ['number' => $stats['domains'], 'total' => ($userFeatures['option_domains'] < 0 ? '∞' : $userFeatures['option_domains'])])); ?></div></div></div>"><?php echo $__env->make('icons.info', ['class' => 'icon-text fill-current'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></a>
                </div>
            </div>

        </div>
        <div class="sidebar sidebar-footer">
            <div class="py-3 <?php echo e((__('lang_dir') == 'rtl' ? 'pr-4 pl-0' : 'pl-4 pr-0')); ?> d-flex align-items-center" aria-expanded="true">
                <a href="<?php echo e(route('settings')); ?>" class="d-flex align-items-center overflow-hidden text-secondary text-decoration-none flex-grow-1">
                    <img src="<?php echo e(gravatar(Auth::user()->email, 72)); ?>" class="flex-shrink-0 rounded-circle <?php echo e((__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')); ?>">

                    <div class="d-flex flex-column text-truncate">
                        <div class="font-weight-medium text-dark text-truncate">
                            <?php echo e(Auth::user()->name); ?>

                        </div>

                        <div class="small font-weight-medium">
                            <?php echo e(__('Settings')); ?>

                        </div>
                    </div>
                </a>

                <a class="py-2 px-4 d-flex flex-shrink-0 align-items-center text-secondary" href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" data-toggle="tooltip" title="<?php echo e(__('Logout')); ?>"><?php echo $__env->make('icons.logout', ['class' => 'fill-current'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></a>

                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?><?php /**PATH /var/www/site3.loc/www/resources/views/shared/header.blade.php ENDPATH**/ ?>