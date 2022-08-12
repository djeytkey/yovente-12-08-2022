@extends('layout.main') @section('content')
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('message') }}</div>
    @endif
    @if (session()->has('not_permitted'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                data-dismiss="alert" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
    @endif

    <section>
        <div class="container-fluid">
            <button class="btn btn-info" data-toggle="modal" data-target="#withdraw-modal"><i class="dripicons-plus"></i>
                {{ trans('file.Add Withdraw') }}</button>
        </div>
        <div class="table-responsive">
            <table id="withdraw-table" class="table">
                <thead>
                    <tr>
                        <th class="not-exported"></th>
                        <th>{{ trans('file.Date') }}</th>
                        <th>{{ trans('file.Reference No') }}</th>
                        <th>{{ trans('file.Amount requested') }}</th>
                        <th>{{ trans('file.Note') }}</th>
                        <th class="not-exported">{{ trans('file.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lims_withdraw_all as $key => $withdraw)
                        <tr data-id="{{ $withdraw->id }}">
                            <td>{{ $key }}</td>
                            <td>{{ date($general_setting->date_format, strtotime($withdraw->created_at->toDateString())) }}
                            </td>
                            <td>{{ $withdraw->reference_no }}</td>
                            <td>{{ number_format((float) $withdraw->withdraw_amount, 2, '.', '') }}</td>
                            <td>{{ $withdraw->withdraw_note }}</td>
                            <td>
                                @if ($withdraw->is_valide != 1)
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                            data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">{{ trans('file.action') }}
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                            user="menu">
                                            @if (in_array('withdraw-edit', $all_permission))
                                                <li><button type="button" data-value="{{ $withdraw->withdraw_amount }}" data-id="{{ $withdraw->id }}"
                                                        class="open-Editwithdraw_categoryDialog btn btn-link"
                                                        data-toggle="modal" data-target="#editModal"><i
                                                            class="dripicons-document-edit"></i>{{ trans('file.edit') }}</button>
                                                </li>
                                            @endif
                                            @if (in_array('withdraw-delete', $all_permission))
                                                <li class="divider"></li>
                                                {{ Form::open(['route' => ['withdraw.destroy', $withdraw->id], 'method' => 'DELETE']) }}
                                                <li>
                                                    <button type="submit" class="btn btn-link"
                                                        onclick="return confirmDelete()"><i
                                                            class="dripicons-trash"></i>{{ trans('file.delete') }}</button>
                                                </li>
                                                {{ Form::close() }}
                                            @endif
                                        </ul>
                                    </div>
                                @else
                                    @if ($withdraw->is_paid == '1')
                                        <div class="badge badge-success">{{ trans('file.Is Paid') }}</div>
                                    @else
                                        @if ($withdraw->is_valide == '1')
                                            <div class="badge badge-success">{{ trans('file.Valid') }}</div>
                                        @endif
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
        class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="exampleModalLabel" class="modal-title">{{ trans('file.Update Withdraw') }}</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                            aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <p class="italic">
                        <small>{{ trans('file.The field labels marked with * are required input fields') }}.</small>
                    </p>
                    {!! Form::open(['route' => ['withdraw.update', 1], 'method' => 'put']) !!}

                    <div class="row">
                        <div class="col-md-12 form-group">
                            <input type="hidden" name="withdraw_id">
                            <label>{{ trans('file.Reference No') }}</label>
                            <h3 id="reference"></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>{{ trans('file.Amount available') }}</label>
                            <h4 id="withdraw_available"></h4>
                            <input type="hidden" name="withdraw_available" class="form-control" />
                        </div>
                        <div class="col-md-6 form-group">
                            <?php
                            $general_setting = \App\GeneralSetting::latest()->first();
                            $min_withdraw = $general_setting->min_withdraw;
                            ?>
                            <label>{{ trans('file.Amount') }} *
                                <small>({{ trans('file.Minimum : ') . $min_withdraw }})</small></label>
                            <input type="number" name="withdraw_amount" min="{{ $min_withdraw }}" step="any" required
                                class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>{{ trans('file.Bank Name') }}</label>
                            <h5>{{ strtoupper(Auth::user()->bank_name) }}</h5>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>{{ trans('file.RIB') }}</label>
                            <h5>{{ Auth::user()->rib }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label>{{ trans('file.Note') }}</label>
                            <textarea name="withdraw_note" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">{{ trans('file.submit') }}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $("ul#withdraw").siblings('a').attr('aria-expanded', 'true');
        $("ul#withdraw").addClass("show");
        $("ul#withdraw #withdraw-list-menu").addClass("active");

        var withdraw_id = [];
        var user_verified = <?php echo json_encode(env('USER_VERIFIED')); ?>;
        var all_permission = <?php echo json_encode($all_permission); ?>;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $(document).on('click', 'button.open-Editwithdraw_categoryDialog', function() {
                var url = "withdraw/";
                var total_available = 0;
                var id = $(this).data('id').toString();
                url = url.concat(id).concat("/edit");
                $.get(url, function(data) {
                    total_available = parseFloat(data['withdraw_available']) + parseFloat(data['withdraw_amount']);
                    $('#editModal #reference').text(data['reference_no']);
                    $('#editModal #withdraw_available').text(total_available);
                    $("#editModal input[name='withdraw_available']").val(total_available);
                    $("#editModal input[name='withdraw_amount']").val(data['withdraw_amount']);
                    $("#editModal input[name='withdraw_id']").val(data['id']);
                    $("#editModal textarea[name='withdraw_note']").val(data['withdraw_note']);
                });
            });
        });

        function confirmDelete() {
            if (confirm("Are you sure want to delete?")) {
                return true;
            }
            return false;
        }

        $('#withdraw-table').DataTable({
            "order": [],
            'language': {
                'lengthMenu': '_MENU_ {{ trans('file.records per page') }}',
                "info": '<small>{{ trans('file.Showing') }} _START_ - _END_ (_TOTAL_)</small>',
                "search": '{{ trans('file.Search') }}',
                'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
                }
            },
            'columnDefs': [{
                    "orderable": false,
                    'targets': [0, 4, 5]
                },
                {
                    'render': function(data, type, row, meta) {
                        if (type === 'display') {
                            data =
                                '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
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
            'select': {
                style: 'multi',
                selector: 'td:first-child'
            },
            'lengthMenu': [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            dom: '<"row"lfB>rtip',
            buttons: [{
                    extend: 'pdf',
                    text: '{{ trans('file.PDF') }}',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                    action: function(e, dt, button, config) {
                        datatable_sum(dt, true);
                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                        datatable_sum(dt, false);
                    },
                    footer: true
                },
                {
                    extend: 'csv',
                    text: '{{ trans('file.CSV') }}',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                    action: function(e, dt, button, config) {
                        datatable_sum(dt, true);
                        $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                        datatable_sum(dt, false);
                    },
                    footer: true
                },
                {
                    extend: 'print',
                    text: '{{ trans('file.Print') }}',
                    exportOptions: {
                        columns: ':visible:Not(.not-exported)',
                        rows: ':visible'
                    },
                    action: function(e, dt, button, config) {
                        datatable_sum(dt, true);
                        $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                        datatable_sum(dt, false);
                    },
                    footer: true
                },
                /*{
                    text: '{{ trans('file.delete') }}',
                    className: 'buttons-delete',
                    action: function(e, dt, node, config) {
                        if (user_verified == '1') {
                            withdraw_id.length = 0;
                            $(':checkbox:checked').each(function(i) {
                                if (i) {
                                    withdraw_id[i - 1] = $(this).closest('tr').data('id');
                                }
                            });
                            if (withdraw_id.length && confirm("Are you sure want to delete?")) {
                                $.ajax({
                                    type: 'POST',
                                    url: 'withdraw/deletebyselection',
                                    data: {
                                        withdrawIdArray: withdraw_id
                                    },
                                    success: function(data) {
                                        alert(data);
                                    }
                                });
                                dt.rows({
                                    page: 'current',
                                    selected: true
                                }).remove().draw(false);
                            } else if (!withdraw_id.length)
                                alert('No withdraw is selected!');
                        } else
                            alert('This feature is disable for demo!');
                    }
                },*/
                {
                    extend: 'colvis',
                    text: '{{ trans('file.Column visibility') }}',
                    columns: ':gt(0)'
                },
            ],
            drawCallback: function() {
                var api = this.api();
                datatable_sum(api, false);
            }
        });

        function datatable_sum(dt_selector, is_calling_first) {
            if (dt_selector.rows('.selected').any() && is_calling_first) {
                var rows = dt_selector.rows('.selected').indexes();
                $(dt_selector.column(5).footer()).html(dt_selector.cells(rows, 5, {
                    page: 'current'
                }).data().sum().toFixed(2));
            } else {
                $(dt_selector.column(5).footer()).html(dt_selector.cells(rows, 5, {
                    page: 'current'
                }).data().sum().toFixed(2));
            }
        }

        if (all_permission.indexOf("withdraw-delete") == -1)
            $('.buttons-delete').addClass('d-none');
    </script>
@endsection
