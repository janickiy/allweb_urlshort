<footer id="footer" class="footer bg-base-0<?php echo e(isset($lightweight) ? ' d-print-none' : ''); ?>">
    <div class="container py-5">
        <?php if(isset($lightweight) == false): ?>
            <div class="row">
                <div class="col-12 col-lg">
                    <ul class="nav p-0 mx-n3 mb-3 mb-lg-0 d-flex flex-column flex-lg-row">
                        <li class="nav-item">
                            <a href="<?php echo e(route('contact')); ?>" class="nav-link py-1"><?php echo e(__('Contact')); ?></a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo e(route('developers')); ?>" class="nav-link py-1"><?php echo e(__('Developers')); ?></a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo e(config('settings.legal_terms_url')); ?>" class="nav-link py-1"><?php echo e(__('Terms')); ?></a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo e(config('settings.legal_privacy_url')); ?>" class="nav-link py-1"><?php echo e(__('Privacy')); ?></a>
                        </li>

                        <?php $__currentLoopData = $footerPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route('page', $page['slug'])); ?>" class="nav-link py-1"><?php echo e(__($page['title'])); ?></a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <div class="col-12 col-lg-auto">
                    <div class="mt-auto py-1 d-flex align-items-center">
                        <?php $__currentLoopData = ['social_facebook' => __('Facebook'), 'social_twitter' => 'Twitter', 'social_instagram' => 'Instagram', 'social_youtube' => 'YouTube']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $url => $title): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(config('settings.'.$url)): ?>
                                <a href="<?php echo e(config('settings.'.$url)); ?>" class="text-secondary text-decoration-none d-flex align-items-center<?php echo e((__('lang_dir') == 'rtl' ? ' ml-3 ml-lg-0 mr-lg-3' : ' mr-3 mr-lg-0 ml-lg-3')); ?>" data-toggle="tooltip" title="<?php echo e($title); ?>" rel="nofollow">
                                    <?php echo $__env->make('icons.share.'.strtolower($title), ['class' => 'fill-current icon-social'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <hr>
        <?php endif; ?>
        <div class="row">
            <div class="col-12 col-lg order-2 order-lg-1">
                <div class="text-muted py-1"><?php echo e(__('© :year :name.', ['year' => now()->year, 'name' => config('settings.title')])); ?> <?php echo e(__('All rights reserved.')); ?></div>
            </div>
            <div class="col-12 col-lg-auto order-1 order-lg-2">
                <?php echo $__env->make('shared.dark_mode', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <?php echo $__env->make('shared.language', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
    </div>

    <?php echo $__env->make('shared.cookie_law', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</footer><?php /**PATH /var/www/site3.loc/www/resources/views/shared/footer.blade.php ENDPATH**/ ?>