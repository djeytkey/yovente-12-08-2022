 <?php $__env->startSection('content'); ?>
<?php if(session()->has('message')): ?>
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo session()->get('message'); ?></div> 
<?php endif; ?>
<?php if(session()->has('not_permitted')): ?>
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e(session()->get('not_permitted')); ?></div> 
<?php endif; ?>

<section class="no-search">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    #<?php echo e($ticket->ticket_id); ?> - <?php echo e($ticket->title); ?>

                </div>
                <div class="panel-body">
                    <?php if(session('status')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>
                    <div class="ticket-info">
                        <p><?php echo e($ticket->message); ?></p>
                        <p>Category: <?php echo e($ticket->category->name); ?></p>
                        <p>
                            <?php if($ticket->status === 'Open'): ?>
                                Status: <span class="label label-success"><?php echo e($ticket->status); ?></span>
                            <?php else: ?>
                                Status: <span class="label label-danger"><?php echo e($ticket->status); ?></span>
                            <?php endif; ?>
                        </p>
                        <p>Created on: <?php echo e($ticket->created_at->diffForHumans()); ?></p>
                    </div>
                </div>
            </div>
            <hr>
            <?php echo $__env->make('tickets.comments', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <hr>
            <?php echo $__env->make('tickets.reply', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</section>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bota4906/noure.alwaleedoptics.com/resources/views/tickets/show.blade.php ENDPATH**/ ?>