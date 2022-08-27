<div class="comments">
    <?php $__currentLoopData = $ticket->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="panel panel-<?php if($ticket->user->id === $comment->user_id): ?><?php echo e("default"); ?><?php else: ?><?php echo e("success"); ?><?php endif; ?>">
            <div class="panel panel-heading">
                <?php echo e($comment->user->name); ?>

                <span class="pull-right"><?php echo e($comment->created_at->format('Y-m-d')); ?></span>
            </div>
            <div class="panel panel-body">
                <?php echo e($comment->comment); ?>

            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div><?php /**PATH /home/bota4906/noure.alwaleedoptics.com/resources/views/tickets/comments.blade.php ENDPATH**/ ?>