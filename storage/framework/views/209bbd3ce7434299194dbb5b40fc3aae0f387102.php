<div class="panel panel-default">
    <div class="panel-heading">Add reply</div>
        <div class="panel-body">
            <div class="comment-form">
                <form action="<?php echo e(url('comment')); ?>" method="POST" class="form">
                    <?php echo csrf_field(); ?>

                    <input type="hidden" name="ticket_id" value="<?php echo e($ticket->id); ?>">
                    <div class="form-group<?php echo e($errors->has('comment') ? ' has-error' : ''); ?>">
                        <textarea rows="10" id="comment" class="form-control" name="comment"></textarea>
                        <?php if($errors->has('comment')): ?>
                            <span class="help-block">
                               <strong><?php echo e($errors->first('comment')); ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
</div><?php /**PATH /home/bota4906/noure.alwaleedoptics.com/resources/views/tickets/reply.blade.php ENDPATH**/ ?>