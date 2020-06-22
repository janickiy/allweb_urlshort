<div class="modal fade" id="deleteLinkModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel"><?php echo e(__('Delete')); ?></h6>
                <button type="button" class="close d-flex align-items-center justify-content-center" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="d-flex align-items-center"><?php echo $__env->make('icons.close', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3"><?php echo e(__('Deleting this link is permanent, and will remove all the data associated with it.')); ?></div>
                <div id="deleteLinkMessage"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo e(__('Close')); ?></button>
                <form action="<?php echo e(isset($admin) ? route('admin.links.delete', 0) : route('links.delete', 0)); ?>" method="post" enctype="multipart/form-data">

                    <?php echo csrf_field(); ?>

                    <button type="submit" class="btn btn-danger"><?php echo e(__('Delete')); ?></button>
                </form>
            </div>
        </div>
    </div>
</div><?php /**PATH /var/www/site3.loc/www/resources/views/shared/modals/delete_link.blade.php ENDPATH**/ ?>