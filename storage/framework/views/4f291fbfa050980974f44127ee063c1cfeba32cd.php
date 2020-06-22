<?php if(isset($options['dropdown']['button'])): ?>
    <button type="button" class="btn text-primary btn-sm d-flex align-items-center <?php echo e((__('lang_dir') == 'rtl' ? 'mr-3' : 'ml-3')); ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $__env->make('icons.horizontal_menu', ['class' => 'fill-current icon-button'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>&#8203;</button>
<?php endif; ?>

<div class="dropdown-menu <?php echo e((__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right')); ?> border-0 shadow">
    <?php if(isset($options['dropdown']['edit'])): ?>
        <a class="dropdown-item d-flex align-items-center" href="<?php echo e(isset($admin) ? route('admin.links.edit', $link->id) : route('links.edit', $link->id)); ?>"><?php echo $__env->make('icons.edit', ['class' => 'text-muted fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> <?php echo e(__('Edit')); ?></a>
    <?php endif; ?>

    <?php if(isset($options['dropdown']['share'])): ?>
        <a class="dropdown-item d-flex align-items-center link-share" href="#" data-toggle="modal" data-target="#shareModal" data-url="<?php echo e((isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias))); ?>" data-title="<?php echo e($link->title ?? str_replace(['http://', 'https://'], '', $link->url)); ?>" data-qr="<?php echo e(route('qr', $link->id)); ?>"><?php echo $__env->make('icons.share', ['class' => 'text-muted fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> <?php echo e(__('Share')); ?></a>
    <?php endif; ?>

    <?php if(isset($options['dropdown']['stats'])): ?>
        <a class="dropdown-item d-flex align-items-center" href="<?php echo e(route('stats', $link->id)); ?>"><?php echo $__env->make('icons.stats', ['class' => 'text-muted fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> <?php echo e(__('Stats')); ?></a>
    <?php endif; ?>

    <?php if(isset($options['dropdown']['preview'])): ?>
        <a class="dropdown-item d-flex align-items-center" href="<?php echo e(isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias)); ?>/+" target="_blank"><?php echo $__env->make('icons.preview', ['class' => 'text-muted fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> <?php echo e(__('Preview')); ?></a>
    <?php endif; ?>

    <?php if(isset($options['dropdown']['open'])): ?>
        <a class="dropdown-item d-flex align-items-center" href="<?php echo e(isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias)); ?>" target="_blank"><?php echo $__env->make('icons.external', ['class' => 'text-muted fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> <?php echo e(__('Open')); ?></a>
    <?php endif; ?>

    <?php if(isset($options['dropdown']['delete'])): ?>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item text-danger d-flex align-items-center" href="#" data-toggle="modal" data-target="#deleteLinkModal" data-action="<?php echo e(isset($admin) ? route('admin.links.delete', $link->id) : route('links.delete', $link->id)); ?>" data-text="<?php echo e(__('Are you sure you want to delete :name?', ['name' => (str_replace(['http://', 'https://'], '', (isset($link->domain) ? $link->domain->name.'/'.$link->alias : route('link.redirect', $link->alias))))])); ?>"><?php echo $__env->make('icons.delete', ['class' => 'fill-current icon-dropdown '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?> <?php echo e(__('Delete')); ?></a>
    <?php endif; ?>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/shared/dropdowns/link.blade.php ENDPATH**/ ?>