@extends('layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div> 
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif

<section>
    <div class="table-responsive">
        <table id="delivery-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Delivery Reference')}}</th>
                    <th>{{trans('file.Sale Reference')}}</th>
                    <th>{{trans('file.customer')}}</th>
                    <th>{{trans('file.Address')}}</th>
                    <th>{{trans('file.Status')}}</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_delivery_all as $key=>$delivery)
                <?php
                    $lims_sale_data = DB::table('sales')->find($delivery->sale_id);
                    // $lims_delivery_status_data = DB::table('delivery_statuses')->where('reference_no', $delivery->reference_no)->get();
                    // foreach ($lims_delivery_status_data as $index => $delivery_status_data) {
                    //     switch ($delivery_status_data->status) {
                    //         case '1':
                    //             $pickup[$index] = $delivery_status_data->status_date;
                    //             break;
                    //         case '2':
                    //             $sent[$index] = $delivery_status_data->status_date;
                    //             break;
                    //         case '3':
                    //             $distribution[$index] = $delivery_status_data->status_date;
                    //             break;
                    //         case '4':
                    //             $delivered[$index] = $delivery_status_data->status_date;
                    //             break;
                    //         case '5':
                    //             $return[$index] = $delivery_status_data->status_date;
                    //             break;
                    //     }
                    // }
                    
                    $barcode = \DNS2D::getBarcodePNG($delivery->reference_no, 'QRCODE');
                ?>
                <tr class="delivery-link" data-barcode="{{$barcode}}" data-delivery='[
                    "{{date($general_setting->date_format, strtotime($delivery->created_at->toDateString()))}}", 
                    "{{$delivery->reference_no}}", 
                    "{{$delivery->sale->reference_no}}", 
                    "Pickup",                                          {{-- "{{$packing}}",  --}}
                    "Pickup Date",                                {{-- "{{$packing_date}}", --}}
                    "Sent",                                      {{-- "{{$packing_date}}", --}}
                    "Sent Date",                                 {{-- "{{$packing_date}}", --}}
                    "Distribution",                                  {{-- "{{$packing_date}}", --}}
                    "Distribution Date",                             {{-- "{{$packing_date}}", --}}
                    "Delivered",                                   {{-- "{{$packing_date}}", --}}
                    "Delivered Date",                              {{-- "{{$packing_date}}", --}}
                    "{{$delivery->id}}",                                {{-- "{{$packing_date}}", --}}
                    "{{$lims_sale_data->customer_name}}",              {{-- "{{$packing_date}}", --}}
                    "{{$lims_sale_data->customer_tel}}",      {{-- "{{$packing_date}}", --}} 
                    "{{$lims_sale_data->customer_address}}",           {{-- "{{$packing_date}}", --}}
                    "{{$lims_sale_data->customer_city}}",              {{-- "{{$packing_date}}", --}}
                    "{{$delivery->note}}",                              {{-- "{{$packing_date}}", --}}
                    "{{$delivery->user->name}}",                        {{-- "{{$packing_date}}", --}}
                    "{{$delivery->delivered_by}}",                      {{-- "{{$packing_date}}", --}}
                    "Received By"                        {{-- "{{$packing_date}}", --}}
                ]'>                  
                    <td>{{$key}}</td>
                    <td>{{ $delivery->reference_no }}</td>
                    <td>{{ $lims_sale_data->reference_no }}</td>
                    <td>{{ $lims_sale_data->customer_name }}</td>
                    <td>{{ $lims_sale_data->customer_address }}</td>
                    <td>This is status</td>
                    {{-- @if($delivery->status == 1)
                    <td><div class="badge badge-warning">{{$status}}</div></td>
                    @elseif($delivery->status == 2)
                    <td><div class="badge badge-info">{{$status}}</div></td>
                    @elseif($delivery->status == 3)
                    <td><div class="badge badge-primary">{{$status}}</div></td>
                    @else
                    <td><div class="badge badge-success">{{$status}}</div></td>
                    @endif --}}
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" data-id="{{$delivery->id}}" class="open-EditCategoryDialog btn btn-link"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</button>
                                </li>
                                <li class="divider"></li>
                                {{ Form::open(['route' => ['delivery.delete', $delivery->id], 'method' => 'post'] ) }}
                                <li>
                                  <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button> 
                                </li>
                                {{ Form::close() }}
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
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
                    <button id="print-btn" type="button" class="btn btn-default btn-sm d-print-none"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>

                    {{ Form::open(['route' => 'delivery.sendMail', 'method' => 'post', 'class' => 'sendmail-form'] ) }}
                        <input type="hidden" name="delivery_id">
                        <button class="btn btn-default btn-sm d-print-none"><i class="dripicons-mail"></i> {{trans('file.Email')}}</button>
                    {{ Form::close() }}
                </div>
                <div class="col-md-6">
                    <h3 id="exampleModalLabel" class="modal-title text-center container-fluid">
                        <img src="{{url('public/logo', $general_setting->site_logo)}}" width="30">
                        {{$general_setting->site_title}}
                    </h3>
                </div>
                <div class="col-md-3">
                    <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close d-print-none"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="col-md-12 text-center">
                    <i style="font-size: 15px;">{{trans('file.Delivery Details')}}</i>
                </div>
            </div>
        </div>
        <div class="modal-body">
            <table class="table table-bordered" id="delivery-content">
                <tbody></tbody>
            </table>
            <br>
            <table class="table table-bordered product-delivery-list">
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

<div id="edit-delivery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="exampleModalLabel" class="modal-title">{{trans('file.Update Delivery')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'delivery.update', 'method' => 'post', 'files' => true]) !!}
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Delivery Reference')}}</label>
                        <p id="dr"></p>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Sale Reference')}}</label>
                        <p id="sr"></p>
                    </div>
                    <div class="col-md-12 form-group">
                        <label>{{trans('file.Status')}} *</label>
                        <select name="status" required class="form-control selectpicker">
                            <option value="1">{{trans('file.Packing')}}</option>
                            <option value="2">{{trans('file.Delivering')}}</option>
                            <option value="3">{{trans('file.Delivered')}}</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-2 form-group">
                        <label>{{trans('file.Delivered By')}}</label>
                        <input type="text" name="delivered_by" class="form-control">
                    </div>
                    <div class="col-md-6 mt-2 form-group">
                        <label>{{trans('file.Recieved By')}}</label>
                        <input type="text" name="recieved_by" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.customer')}} *</label>
                        <p id="customer"></p>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Attach File')}}</label>
                        <input type="file" name="file" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Address')}} *</label>
                        <textarea rows="3" name="address" class="form-control" required></textarea>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>{{trans('file.Note')}}</label>
                        <textarea rows="3" name="note" class="form-control"></textarea>
                    </div>
                </div>
                <input type="hidden" name="reference_no">
                <input type="hidden" name="delivery_id">
                <button type="submit" class="btn btn-primary">{{trans('file.submit')}}</button>
                {{ Form::close() }}
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
          newWin.document.write('<link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css"><style type="text/css">@media print {.modal-dialog { max-width: 1000px;} }</style><body onload="window.print()">'+divToPrint.innerHTML+'</body>');
          newWin.document.close();
          setTimeout(function(){newWin.close();},10);
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
        $('input[name="delivery_id"]').val(delivery[11]);
        $("#delivery-content tbody").remove();
        var newBody = $("<tbody>");
        var rows = status = '';
        var packing = delivery[3];
        var packing_date = delivery[4];
        var pickup = delivery[5];
        var pickup_date = delivery[6];
        var delivering = delivery[7];
        var delivering_date = delivery[8];
        var delivered = delivery[9];
        var delivered_date = delivery[10];
        if (packing_date !== '') {
            status += '<div class="col-md-3 badge badge-warning lh-1-5">'+packing+'<br>'+packing_date+'</div>';
        }
        if (pickup_date !== '') {
            status += '<div class="col-md-3 badge badge-info lh-1-5">'+pickup+'<br>'+pickup_date+'</div>';
        }
        if (delivering_date !== '') {
            status += '<div class="col-md-3 badge badge-primary lh-1-5">'+delivering+'<br>'+delivering_date+'</div>';
        }
        if (delivered_date !== '') {
            status += '<div class="col-md-3 badge badge-success lh-1-5">'+delivered+'<br>'+delivered_date+'</div>';
        }
        rows += '<tr><td>Date</td><td>'+delivery[0]+'</td></tr>';
        rows += '<tr><td>Delivery Reference</td><td>'+delivery[1]+'</td></tr>';
        rows += '<tr><td>Sale Reference</td><td>'+delivery[2]+'</td></tr>';
        rows += '<tr><td>Status</td><td>'+status+'</td></tr>';
        rows += '<tr><td>Customer Name</td><td>'+delivery[12]+'</td></tr>';
        rows += '<tr><td>Address</td><td>'+delivery[14]+', '+delivery[15]+'</td></tr>';
        rows += '<tr><td>Phone Number</td><td>'+delivery[13]+'</td></tr>';
        rows += '<tr><td>Note</td><td>'+delivery[16]+'</td></tr>';

        newBody.append(rows);
        $("table#delivery-content").append(newBody);

        $.get('delivery/product_delivery/' + delivery[11], function(data) {
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

        var htmlfooter = '<div class="col-md-4 form-group"><p>Prepared By: '+delivery[17]+'</p></div>';
        htmlfooter += '<div class="col-md-4 form-group"><p>Delivered By: '+delivery[18]+'</p></div>';
        htmlfooter += '<div class="col-md-4 form-group"><p>Recieved By: '+delivery[19]+'</p></div>';
        htmlfooter += '<br><br><br><br>';
        htmlfooter += '<div class="col-md-2 offset-md-5"><img style="max-width:850px;height:100%;max-height:130px" src="data:image/png;base64,'+barcode+'" alt="barcode" /></div>';

        $('#delivery-footer').html(htmlfooter);
        $('#delivery-details').modal('show');
    }

    $(document).ready(function() {
        $('.open-EditCategoryDialog').on('click', function(){
          var url ="delivery/"  
          var id = $(this).data('id').toString();
          url = url.concat(id).concat("/edit");
          
          $.get(url, function(data){
                $('#dr').text(data[0]);
                $('#sr').text(data[1]);
                $('select[name="status"]').val(data[2]);
                $('.selectpicker').selectpicker('refresh');
                $('input[name="delivered_by"]').val(data[3]);
                $('input[name="recieved_by"]').val(data[4]);
                $('#customer').text(data[5]);
                $('textarea[name="address"]').val(data[6]);
                $('textarea[name="note"]').val(data[7]);
                $('input[name="reference_no"]').val(data[0]);
                $('input[name="delivery_id"]').val(id);
          });
          $("#edit-delivery").modal('show');
        });
    });

    $('#delivery-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
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
                text: '{{trans("file.PDF")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'csv',
                text: '{{trans("file.CSV")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                extend: 'print',
                text: '{{trans("file.Print")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
            },
            {
                text: '{{trans("file.delete")}}',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        delivery_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                delivery_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(delivery_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'delivery/deletebyselection',
                                data:{
                                    deliveryIdArray: delivery_id
                                },
                                success:function(data){
                                    alert(data);
                                }
                            });
                            dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!delivery_id.length)
                            alert('Nothing is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '{{trans("file.Column visibility")}}',
                columns: ':gt(0)'
            },
        ],
    } );
</script>
@endsection