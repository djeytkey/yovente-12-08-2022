 <?php $__env->startSection('content'); ?>
<?php if(session()->has('message')): ?>
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo session()->get('message'); ?></div> 
<?php endif; ?>
<?php if(session()->has('not_permitted')): ?>
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e(session()->get('not_permitted')); ?></div> 
<?php endif; ?>

<section class="no-search">
    <div class="table-responsive">
        <table id="delivery-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th><?php echo e(trans('file.Delivery Reference')); ?></th>
                    <th><?php echo e(trans('file.Sale Reference')); ?></th>
                    <th><?php echo e(trans('file.customer')); ?></th>
                    <th><?php echo e(trans('file.Phone')); ?></th>
                    <th><?php echo e(trans('file.Address')); ?></th>
                    <th><?php echo e(trans('file.City')); ?></th>
                    <th><?php echo e(trans('file.Status')); ?></th>
                    <th class="not-exported"><?php echo e(trans('file.action')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $lims_delivery_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $barcode = \DNS2D::getBarcodePNG($delivery->reference_no, 'QRCODE');
                    $lims_sale_data = DB::table('sales')->find($delivery->sale_id);
                    $lims_delivery_status_data = DB::table('delivery_statuses')->where('reference_no', $delivery->reference_no)->orderBy('id', 'desc')->first();
                    switch ($lims_delivery_status_data->status) {
                        case "1":
                            $badge = "warning";
                            $status = "Pickup";
                            break;
                        case "2":
                            $badge = "info";
                            $status = "Sent";
                            break;
                        case "3":
                            $badge = "primary";
                            $status = "Distribution";
                            break;
                        case "4":
                            $badge = "success";
                            $status = "Delivered";
                            break;
                        case "5":
                            $badge = "danger";
                            $status = "Ne répond pas";
                            break;
                        case "6":
                            $badge = "danger";
                            $status = "Injoignable";
                            break;
                        case "7":
                            $badge = "danger";
                            $status = "Erreur numéro";
                            break;
                        case "8":
                            $badge = "danger";
                            $status = "Reporté";
                            break;
                        case "9":
                            $badge = "danger";
                            $status = "Programmé";
                            break;
                        case "10":
                            $badge = "danger";
                            $status = "Annulé";
                            break;
                        case "11":
                            $badge = "danger";
                            $status = "Refusé";
                            break;
                        case "12":
                            $badge = "danger";
                            $status = "Retourné";
                            break;
                    }
                    $status_date = $lims_delivery_status_data->status_date;
                    $lims_city_data = DB::table('cities')->where('id', $lims_sale_data->customer_city)->first();
                ?>
                <tr class="delivery-link" data-barcode="<?php echo e($barcode); ?>" data-delivery='[
                    "<?php echo e(date($general_setting->date_format, strtotime($delivery->created_at->toDateString()))); ?>", 
                    "<?php echo e($delivery->reference_no); ?>", 
                    "<?php echo e($delivery->sale->reference_no); ?>", 
                    "<?php echo e($delivery->id); ?>",
                    "<?php echo e($lims_sale_data->customer_name); ?>",
                    "<?php echo e($lims_sale_data->customer_tel); ?>", 
                    "<?php echo e($lims_sale_data->customer_address); ?>",
                    "<?php echo e($lims_city_data->name); ?>",
                    "<?php echo e($delivery->note); ?>",
                    "<?php echo e($delivery->user->name); ?>",
                    "<?php echo e($delivery->delivered_by); ?>"   
                ]'>                  
                    <td><?php echo e($key); ?></td>
                    <td><?php echo e($delivery->reference_no); ?></td>
                    <td><?php echo e($lims_sale_data->reference_no); ?></td>
                    <td><?php echo e($lims_sale_data->customer_name); ?></td>
                    <td><?php echo e($lims_sale_data->customer_tel); ?></td>
                    <td><?php echo e($lims_sale_data->customer_address); ?></td>
                    <td><?php echo e($lims_city_data->name); ?></td>
                    <td><div class="badge badge-<?php echo e($badge); ?>"><?php echo e($status); ?><br><?php echo e($status_date); ?></div></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo e(trans('file.action')); ?>

                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                
                                <li>
                                    <button type="button" class="add-delivery btn btn-link" data-id = "<?php echo e($lims_sale_data->id); ?>"><i class="fa fa-truck"></i> <?php echo e(trans('file.Add Delivery')); ?></button>
                                </li>
                                <li class="divider"></li>
                                <?php echo e(Form::open(['route' => ['delivery.delete', $delivery->id], 'method' => 'post'] )); ?>

                                <li>
                                  <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> <?php echo e(trans('file.delete')); ?></button> 
                                </li>
                                <?php echo e(Form::close()); ?>

                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Modal -->
<div id="delivery-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
      <div class="modal-content">
        <div class="container mt-3 pb-2 border-bottom">
            <div class="row">
                <div class="col-md-3">
                    <button id="print-btn" type="button" class="btn btn-default btn-sm d-print-none"><i class="dripicons-print"></i> <?php echo e(trans('file.Print')); ?></button>

                    <?php echo e(Form::open(['route' => 'delivery.sendMail', 'method' => 'post', 'class' => 'sendmail-form'] )); ?>

                        <input type="hidden" name="delivery_id">
                        <button class="btn btn-default btn-sm d-print-none"><i class="dripicons-mail"></i> <?php echo e(trans('file.Email')); ?></button>
                    <?php echo e(Form::close()); ?>

                </div>
                <div class="col-md-6">
                    <h3 id="exampleModalLabel" class="modal-title text-center container-fluid">
                        <img src="<?php echo e(url('public/logo', $general_setting->site_logo)); ?>" width="30">
                        <?php echo e($general_setting->site_title); ?>

                    </h3>
                </div>
                <div class="col-md-3">
                    <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close d-print-none"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="col-md-12 text-center">
                    <i style="font-size: 15px;"><?php echo e(trans('file.Delivery Details')); ?></i>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <table class="table modal-table table-bordered" id="delivery-content">
                <tbody></tbody>
            </table>
            <br>
            <table class="table modal-table table-bordered delivery-status-list">
                <tbody></tbody>
            </table>
            <br>
            <table class="table modal-table table-bordered product-delivery-list">
                <thead>
                    <th>No</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Qty</th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="delivery-footer" class="row">
            </div>            
        </div>    
      </div>
    </div>
</div>

<div id="add-delivery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title"><?php echo e(trans('file.Add Delivery')); ?></h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <?php echo Form::open(['route' => 'delivery.store', 'method' => 'post', 'files' => true, 'class' => 'delivery-form']); ?>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label><strong><?php echo e(trans('file.Delivery Reference')); ?> *</strong></label>
                        <p id="dr"></p>
                    </div>
                    <div class="col-md-6 form-group">
                        <label><strong><?php echo e(trans('file.Sale Reference')); ?> *</strong></label>
                        <p id="sr"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label><strong><?php echo e(trans('file.Status')); ?> *</strong></label>
                        <select required name="status" id="status" class="selectpicker form-control" data-live-search="true" title="Select status...">
                            <option value="1"><?php echo e(trans('file.Pickup')); ?></option>
                            <option value="2"><?php echo e(trans('file.Sent')); ?></option>
                            <option value="3"><?php echo e(trans('file.Distribution')); ?></option>
                            <option value="4"><?php echo e(trans('file.Livré')); ?></option>
                            <option value="5"><?php echo e(trans('file.Ne répond pas')); ?></option>
                            <option value="6"><?php echo e(trans('file.Injoignable')); ?></option>
                            <option value="7"><?php echo e(trans('file.Erreur numéro')); ?></option>
                            <option value="8"><?php echo e(trans('file.Reporté')); ?></option>
                            <option value="9"><?php echo e(trans('file.Programmé')); ?></option>
                            <option value="10"><?php echo e(trans('file.Annulé')); ?></option>
                            <option value="11"><?php echo e(trans('file.Refusé')); ?></option>
                            <option value="12"><?php echo e(trans('file.Retourné')); ?></option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label><strong><?php echo e(trans('file.Date')); ?> *</strong></label>
                        <div class="input-group" >
                            <input type="text" class="form-control" id="dtpicker_delivery" name="status_date" required readonly>
                            <div class="input-group-addon">
                                <span class="glyphicon glyphicon-th"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 form-group">
                        <label><strong><?php echo e(trans('file.Delivered By')); ?> *</strong></label>
                        <input type="text" name="delivered_by" class="form-control" required>
                    </div>                                            
                </div>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label><strong><?php echo e(trans('file.customer')); ?> *</strong></label>
                        <p id="customer"></p>
                    </div>
                    <div class="col-md-6 form-group">
                        <label><strong><?php echo e(trans('file.Note')); ?></strong></label>
                        <textarea rows="3" name="note" class="form-control"></textarea>
                    </div>
                </div>
                <input type="hidden" name="reference_no">
                <input type="hidden" name="sale_id">
                <input type="hidden" name="is_close" value="0">
                <button type="submit" class="btn btn-primary"><?php echo e(trans('file.submit')); ?></button>
                <?php echo e(Form::close()); ?>

            </div>
        </div>
    </div>
</div>



<script type="text/javascript">

    $("ul#sale").siblings('a').attr('aria-expanded','true');
    $("ul#sale").addClass("show");
    $("ul#sale #delivery-menu").addClass("active");

    var delivery_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#print-btn").on("click", function(){
          var divToPrint=document.getElementById('delivery-details');
          var newWin=window.open('','Print-Window');
          newWin.document.open();
          newWin.document.write('<link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css"><style type="text/css">@media  print {.modal-dialog { max-width: 1000px;} }</style><body onload="window.print()">'+divToPrint.innerHTML+'</body>');
          newWin.document.close();
          setTimeout(function(){newWin.close();},10);
    });

    $('select[name="status"]').on("change", function() {
        var id = $(this).val();
        if(id == "4")
            $('input[name="is_close"]').val("1");
        else {
            $('input[name="is_close"]').val("0");
        }
    });


    function confirmDelete() {
      if (confirm("Are you sure want to delete?")) {
          return true;
      }
      return false;
    }

    $("tr.delivery-link td:not(:first-child, :last-child)").on("click", function() {
        var delivery = $(this).parent().data('delivery');
        var barcode = $(this).parent().data('barcode');
        deliveryDetails(delivery, barcode);
    });

    function deliveryDetails(delivery, barcode) {
        $('input[name="delivery_id"]').val(delivery[3]);
        $("#delivery-content tbody").remove();
        var newBody = $("<tbody>");
        var rows = status = '';
        rows += '<tr><td>Date</td><td>'+delivery[0]+'</td></tr>';
        rows += '<tr><td>Delivery Reference</td><td>'+delivery[1]+'</td></tr>';
        rows += '<tr><td>Sale Reference</td><td>'+delivery[2]+'</td></tr>';
        rows += '<tr><td>Customer Name</td><td>'+delivery[4]+'</td></tr>';
        rows += '<tr><td>Phone</td><td>'+delivery[5]+'</td></tr>';
        rows += '<tr><td>Address</td><td>'+delivery[6]+', '+delivery[7]+'</td></tr>';
        rows += '<tr><td>Note</td><td>'+delivery[8]+'</td></tr>';        

        newBody.append(rows);
        $("table#delivery-content").append(newBody);

        $.get('delivery/delivery_status/' + delivery[3], function(data) {
            $(".delivery-status-list tbody").remove();
            var status = data[0];
            var status_date = data[1];
            var newBody = $("<tbody>");
            $.each(status, function(index) {
                var newRow = $("<tr>");
                var cols = '';
                switch(status[index]) {
                    case "1":
                        cols += '<td><div class="badge badge-warning">Pickup</div></td>';
                        break;
                    case "2":
                        cols += '<td><div class="badge badge-info">Sent</div></td>';
                        break;
                    case "3":
                        cols += '<td><div class="badge badge-primary">Mise en distribution</div></td>';
                        break;
                    case "4":
                        cols += '<td><div class="badge badge-success">Delivered</div></td>';
                        break;
                    case "5":
                        cols += '<td><div class="badge badge-danger">Ne répond pas</div></td>';
                        break;
                    case "6":
                        cols += '<td><div class="badge badge-danger">Injoignable</div></td>';
                        break;
                    case "7":
                        cols += '<td><div class="badge badge-danger">Erreur numéro</div></td>';
                        break;
                    case "8":
                        cols += '<td><div class="badge badge-danger">Reporté</div></td>';
                        break;
                    case "9":
                        cols += '<td><div class="badge badge-danger">Programmé</div></td>';
                        break;
                    case "10":
                        cols += '<td><div class="badge badge-danger">Annulé</div></td>';
                        break;
                    case "11":
                        cols += '<td><div class="badge badge-danger">Refusé</div></td>';
                        break;
                    case "12":
                        cols += '<td><div class="badge badge-danger">Retourné</div></td>';
                        break;
                }
                
                cols += '<td>' + status_date[index] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);
            });
            $("table.delivery-status-list").append(newBody);
        });

        $.get('delivery/product_delivery/' + delivery[3], function(data) {
            $(".product-delivery-list tbody").remove();
            var code = data[0];
            var description = data[1];
            var qty = data[2];
            var newBody = $("<tbody>");
            $.each(code, function(index) {
                var newRow = $("<tr>");
                var cols = '';
                cols += '<td><strong>' + (index+1) + '</strong></td>';
                cols += '<td>' + code[index] + '</td>';
                cols += '<td>' + description[index] + '</td>';
                cols += '<td>' + qty[index] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);
            });
            $("table.product-delivery-list").append(newBody);
        });

        var htmlfooter = '<div class="col-md-6 form-group"><p>Delivered By: '+delivery[10]+'</p></div>';
        htmlfooter += '<div class="col-md-6 form-group"><img style="max-width:850px;height:100%;max-height:130px" src="data:image/png;base64,'+barcode+'" alt="barcode" /></div>';
        htmlfooter += '<br><br><br><br>';
        htmlfooter += '';

        $('#delivery-footer').html(htmlfooter);
        $('#delivery-details').modal('show');
    }

    $(document).ready(function() {
        $(document).on("click", "table#delivery-table tbody .add-delivery", function(event) {
            var id = $(this).data('id').toString();
            $.get('delivery/create/'+id, function(data) {
                $('#dr').text(data[0]);
                $('#sr').text(data[1]);
                $('input[name="delivered_by"]').val(data[6]);
                $('#customer').html(data[2] + "<br>" + data[3] + "<br>" + data[4] + "<br>" + data[5]);
                $('textarea[name="note"]').val(data[7]);
                $('input[name="status_date"]').val(data[8]);
                $('input[name="reference_no"]').val(data[0]);
                $('input[name="sale_id"]').val(id);            
                $('#add-delivery').modal('show');
            });
        });
    });

    $('#delivery-table').DataTable( {
        "order": [],
        "searching": false,
        'language': {
            'lengthMenu': '_MENU_ <?php echo e(trans("file.records per page")); ?>',
             "info":      '<small><?php echo e(trans("file.Showing")); ?> _START_ - _END_ (_TOTAL_)</small>',
            "search":  '<?php echo e(trans("file.Search")); ?>',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 6]
            },
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
                extend: 'pdf',
                text: '<?php echo e(trans("file.PDF")); ?>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'csv',
                text: '<?php echo e(trans("file.CSV")); ?>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'print',
                text: '<?php echo e(trans("file.Print")); ?>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            // {
            //     text: '<?php echo e(trans("file.delete")); ?>',
            //     className: 'buttons-delete',
            //     action: function ( e, dt, node, config ) {
            //         if(user_verified == '1') {
            //             delivery_id.length = 0;
            //             $(':checkbox:checked').each(function(i){
            //                 if(i){
            //                     delivery_id[i-1] = $(this).closest('tr').data('id');
            //                 }
            //             });
            //             if(delivery_id.length && confirm("Are you sure want to delete?")) {
            //                 $.ajax({
            //                     type:'POST',
            //                     url:'delivery/deletebyselection',
            //                     data:{
            //                         deliveryIdArray: delivery_id
            //                     },
            //                     success:function(data){
            //                         alert(data);
            //                     }
            //                 });
            //                 dt.rows({ page: 'current', selected: true }).remove().draw(false);
            //             }
            //             else if(!delivery_id.length)
            //                 alert('Nothing is selected!');
            //         }
            //         else
            //             alert('This feature is disable for demo!');
            //     }
            // },
            {
                extend: 'colvis',
                text: '<?php echo e(trans("file.Column visibility")); ?>',
                columns: ':gt(0)'
            },
        ],
    } );
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\yovente\resources\views/delivery/index.blade.php ENDPATH**/ ?>