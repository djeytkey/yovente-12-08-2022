 <?php $__env->startSection('content'); ?>
<?php if(session()->has('message')): ?>
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo session()->get('message'); ?></div> 
<?php endif; ?>
<?php if(session()->has('not_permitted')): ?>
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e(session()->get('not_permitted')); ?></div> 
<?php endif; ?>

<section class="no-search">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center"><?php echo e(trans('file.Ticket List')); ?></h3>
            </div>
            <?php echo Form::open(['route' => 'tickets.index', 'method' => 'get']); ?>

            <div class="row no-mrl mb-3">
                <div class="col-md-4 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong><?php echo e(trans('file.Choose Your Date')); ?></strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input type="text" class="daterangepicker-field form-control" value="<?php echo e($starting_date); ?> To <?php echo e($ending_date); ?>" required />
                                <input type="hidden" name="starting_date" value="<?php echo e($starting_date); ?>" />
                                <input type="hidden" name="ending_date" value="<?php echo e($ending_date); ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong><?php echo e(trans('file.Search')); ?></strong> &nbsp;</label>
                        <div class="d-tc">
                            <input type="text" name="search_string" id="search_string" class="form-control" placeholder="<?php echo e(trans('file.Type to search...')); ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong><?php echo e(trans('file.Choose Status')); ?></strong> &nbsp;</label>
                        <div class="d-tc">
                            <select id="status_id" name="status_id" class="selectpicker form-control" data-live-search="true" >
                                <option value="2"><?php echo e(trans('file.All Status')); ?></option>
                                <option value="1"><?php echo e(trans('file.Open')); ?></option>
                                <option value="0"><?php echo e(trans('file.Closed')); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary" id="filter-btn" type="submit"><?php echo e(trans('file.submit')); ?></button>
                    </div>
                </div>
            </div>
            <?php echo Form::close(); ?>

        </div>
        
    </div>
    <div class="table-responsive">
        <table id="ticket-table" class="table ticket-list stripe" style="width: 100%">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th><?php echo e(trans('file.Date')); ?></th>
                    <th><?php echo e(trans('file.reference')); ?></th>
                    <th><?php echo e(trans('file.title')); ?></th>
                    <th><?php echo e(trans('file.priority')); ?></th>
                    <th><?php echo e(trans('file.category')); ?></th>
                    <th><?php echo e(trans('file.status')); ?></th>
                    <th><?php echo e(trans('file.Last Updated')); ?></th>
                    <th class="not-exported"><?php echo e(trans('file.action')); ?></th>
                </tr>
            </thead>
            
            <tfoot class="tfoot active">
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>                
            </tfoot>
        </table>
    </div>
</section>

<script type="text/javascript">

    $("ul#ticket").siblings('a').attr('aria-expanded','true');
    $("ul#ticket").addClass("show");
    $("ul#ticket #ticket-list-menu").addClass("active");
    var ticket_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".daterangepicker-field").daterangepicker({
      callback: function(startDate, endDate, period){
        var starting_date = startDate.format('YYYY-MM-DD');
        var ending_date = endDate.format('YYYY-MM-DD');
        var title = starting_date + ' To ' + ending_date;
        $(this).val(title);
        $('input[name="starting_date"]').val(starting_date);
        $('input[name="ending_date"]').val(ending_date);
      }
    });

    $('.selectpicker').selectpicker('refresh');

    var queryString = window.location.search;
    var urlParams = new URLSearchParams(queryString);
    var status = urlParams.get('status_id');
    $('select[name=status_id]').val(status);
    $('.selectpicker').selectpicker('refresh');
    var searchstring = urlParams.get('search_string');
    $('input[name=search_string]').val(searchstring);

    var starting_date = $("input[name=starting_date]").val(); 
    var ending_date = $("input[name=ending_date]").val();
    var status_id = $("#status_id").val();
    var search_string = $("#search_string").val();

    $('#ticket-table').DataTable( {
        "processing": true,
        "serverSide": true,
        "searching": false,
        "ajax":{
            url:"tickets/ticket-data",
            data:{
                starting_date: starting_date,
                ending_date: ending_date,
                status_id: status_id,
                search_string: search_string
            },
            dataType: "json",
            type:"post"
        },
        "createdRow": function( row, data, dataIndex ) {
            $(row).addClass('ticket-link');
            $(row).attr('data-ticket', data['ticket']);
        },
        "columns": [
            {"data": "key"},
            {"data": "date"},
            {"data": "ticket_id"},
            {"data": "title"},
            {"data": "priority"},
            {"data": "category"},
            {"data": "status"},
            {"data": "last_update"},
            {"data": "options"},
        ],
        'language': {            
            'lengthMenu': '_MENU_ <?php echo e(trans("file.records per page")); ?>',
             "info":      '<small><?php echo e(trans("file.Showing")); ?> _START_ - _END_ (_TOTAL_)</small>',
            "search":  '<?php echo e(trans("file.Search")); ?>',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        order:[['1', 'desc']],
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 2, 3, 5, 7, 8],
            },
            // {
            //     'targets': 3,
            //     className: 'noVis'
            // },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'colvis',
                text: '<?php echo e(trans("file.Column visibility")); ?>',
                //columns: ':not(.noVis)'
                columns: ':gt(0)'
            },
        ]
    } );

</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/bota4906/noure.alwaleedoptics.com/resources/views/tickets/index.blade.php ENDPATH**/ ?>