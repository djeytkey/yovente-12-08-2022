<?php
namespace App\Http\Controllers;
use App\Ticket;
use Illuminate\Http\Request;
use Auth;
class TicketsController extends Controller
{    
    public function index(Request $request)
    {
        if($request->input('status_id'))
            $status_id = $request->input('status_id');
        else
            $status_id = 2;

        if($request->input('starting_date')) {
            $starting_date = $request->input('starting_date');
            $ending_date = $request->input('ending_date');
        }
        else {
            $starting_date = date("Y") . "-01-01";
            $ending_date = date("Y-m-d");
        }

        $tickets = Ticket::paginate(10);
        return view('tickets.index', compact('starting_date', 'ending_date', 'status_id', 'tickets'));
    }
    
    public function create()
    {
        //
    }

    public function ticketData(Request $request)
    {
        $columns = array( 
            1 => 'created_at', 
            2 => 'ticket_id',
            4 => 'priority',
            6 => 'status',
        );

        if($request->input('status_id') != "All")
            $status_id = $request->input('status_id');
        else
            $status_id = 2;
        
        if(!empty($request->input('staring_date')))
            $staring_date = $request->input('staring_date');
        else
            $staring_date = "";

        if(!empty($request->input('ending_date')))
            $ending_date = $request->input('ending_date');
        else
            $ending_date = "";

        if(!empty($request->input('search_string')))
            $search_string = $request->input('search_string');
        else
            $search_string = "";
        
        if(Auth::user()->role_id > 2 && config('staff_access') == 'own' && $status_id != 2)
            $totalData = Sale::where([
                                        ['user_id', Auth::id()],
                                        ['is_valide', $status_id]
                                    ])
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->count();
        elseif(Auth::user()->role_id > 2 && config('staff_access') == 'own')
            $totalData = Sale::where('user_id', Auth::id())
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->count();
        elseif($status_id != 2)
            $totalData = Sale::where('is_valide', $status_id)
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->count();
        else
            $totalData = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->count();

        $totalFiltered = $totalData;
        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = 'sales.'.$columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $condition = "vide";
        if(empty($request->input('search_string'))) {
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own' && $status_id != 2)
                $sales = Sale::where([
                            ['user_id', Auth::id()],
                            ['is_valide', $status_id]
                        ])
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            elseif(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $sales = Sale::where('user_id', Auth::id())
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            elseif($status_id != 2)
                $sales = Sale::where('is_valide', $status_id)
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            else
                $sales = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
        }
        else
        {
            $search = $request->input('search_string');
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own' && $status_id != 2)
            {
                $condition = "Auth::user()->role_id > 2 && config('staff_access') == 'own' && status_id != 2";
                $sales = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['user_id', Auth::id()],
                            ['is_valide', $status_id],
                            ['reference_no', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['is_valide', $status_id],
                            ['customer_name', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['is_valide', $status_id],
                            ['customer_tel', 'LIKE', "%{$search}%"]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                $totalFiltered = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['user_id', Auth::id()],
                            ['is_valide', $status_id],
                            ['reference_no', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['is_valide', $status_id],
                            ['customer_name', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['is_valide', $status_id],
                            ['customer_tel', 'LIKE', "%{$search}%"]
                        ])
                        ->count();
            }                
            elseif(Auth::user()->role_id > 2 && config('staff_access') == 'own')
            {
                $condition = "Auth::user()->role_id > 2 && config('staff_access') == 'own'";
                $sales = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['user_id', Auth::id()],
                            ['reference_no', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['customer_name', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['customer_tel', 'LIKE', "%{$search}%"]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                $totalFiltered = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['user_id', Auth::id()],
                            ['reference_no', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['customer_name', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['customer_tel', 'LIKE', "%{$search}%"]
                        ])
                        ->count();
            }                
            elseif($status_id != 2)
            {
                $condition = "status_id != 2";
                $sales = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['is_valide', $status_id],
                            ['reference_no', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['is_valide', $status_id],
                            ['customer_name', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['is_valide', $status_id],
                            ['customer_tel', 'LIKE', "%{$search}%"]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                $totalFiltered = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['is_valide', $status_id],
                            ['reference_no', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['is_valide', $status_id],
                            ['customer_name', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['is_valide', $status_id],
                            ['customer_tel', 'LIKE', "%{$search}%"]
                        ])
                        ->count();
            }
            else
            {
                $condition = "else";
                $sales = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where('reference_no', 'LIKE', "%{$search}%")
                        ->orwhere('customer_name', 'LIKE', "%{$search}%")
                        ->orwhere('customer_tel', 'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                $totalFiltered = Sale::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where('reference_no', 'LIKE', "%{$search}%")
                        ->orwhere('customer_name', 'LIKE', "%{$search}%")
                        ->orwhere('customer_tel', 'LIKE', "%{$search}%")
                        ->count();
            }
        }
        
        $data = array();
        if(!empty($sales))
        {
            foreach ($sales as $key=>$sale)
            {
                $nestedData['id'] = $sale->id;
                $nestedData['key'] = $key;
                $nestedData['sold_by'] = $sale->user_id;
                $nestedData['date'] = date(config('date_format'), strtotime($sale->created_at->toDateString()));
                $nestedData['reference_no'] = $sale->reference_no;
                $nestedData['customer'] = $sale->customer_name;
                $nestedData['phone'] = $sale->customer_tel;
                $nestedData['username'] = User::find($nestedData['sold_by'])->name; 

                $lims_city_data = City::where('id', $sale->customer_city)->first();
                $nestedData['city'] = $lims_city_data->name;
				$sale_city = $lims_city_data->name;

                if($sale->is_valide == 1)
                {
                    $nestedData['valide_status'] = '<div class="badge badge-success">'.trans('file.Confirmed').'</div>';
                    $nestedData['valide_status_search'] = trans('file.Confirmed');
                    
                } else {
                    $nestedData['valide_status'] = '<div class="badge badge-warning">'.trans('file.Not Confirmed').'</div>';
                    $nestedData['valide_status_search'] = trans('file.Not Confirmed');
                }

                $lims_product_sale_data = Product_Sale::where('sale_id', $sale->id)->get();
                $product_qty = "";
                $variant_name = "";
                $product_name = "";

                if (!empty($lims_product_sale_data))
                {
                    $nestedData['products'] = '<ul class="table-products">';
                    foreach ($lims_product_sale_data as $key => $product_sale_data) {
                        $lims_product_data = Product::find($product_sale_data->product_id);
                        $product_name = $lims_product_data->name;
                        $product_qty = $product_sale_data->qty;
                        if($product_sale_data->variant_id) {
                            $variant_data = Variant::select('name')->find($product_sale_data->variant_id);
                            $variant_name = $variant_data->name;
                        }
                        else
                            $variant_name = "";
                        
                        $nestedData['products'] .= '<li class="single-table-product">' . $product_name . '&nbsp;(&nbsp;' . str_pad($product_qty, 2, '0', STR_PAD_LEFT) . '&nbsp;/&nbsp;' . $variant_name . '&nbsp;)</li>';
                    }
                    $nestedData['products'] .= '</ul>';
                } else {
                    $nestedData['products'] = "--";
                }

                if($sale->sale_status == 1){
                    $nestedData['sale_status'] = '<div class="badge badge-success">'.trans('file.Completed').'</div>';
                    $sale_status = trans('file.Completed');
                }
                elseif($sale->sale_status == 2){
                    $nestedData['sale_status'] = '<div class="badge badge-danger">'.trans('file.Pending').'</div>';
                    $sale_status = trans('file.Pending');
                }
                else{
                    $nestedData['sale_status'] = '<div class="badge badge-warning">'.trans('file.Draft').'</div>';
                    $sale_status = trans('file.Draft');
                }

                if($sale->payment_status == 1)
                    $nestedData['payment_status'] = '<div class="badge badge-danger">'.trans('file.Pending').'</div>';
                elseif($sale->payment_status == 2)
                    $nestedData['payment_status'] = '<div class="badge badge-danger">'.trans('file.Due').'</div>';
                elseif($sale->payment_status == 3)
                    $nestedData['payment_status'] = '<div class="badge badge-warning">'.trans('file.Partial').'</div>';
                else
                    $nestedData['payment_status'] = '<div class="badge badge-success">'.trans('file.Paid').'</div>';                

                $nestedData['grand_total'] = number_format($sale->grand_total, 2);
                $nestedData['paid_amount'] = number_format($sale->paid_amount, 2);
                $nestedData['due'] = number_format($sale->grand_total - $sale->paid_amount, 2);
                $lims_delivery_data = Delivery::where('sale_id', $sale->id)->first();
                if ($lims_delivery_data) {
                    $lims_delivery_status_data = DeliveryStatus::where('reference_no', $lims_delivery_data->reference_no)->orderBy('id', 'desc')->first();
                    switch ($lims_delivery_status_data->status) {
                        case "1":
                            $nestedData['delivery_status'] = '<div class="badge badge-warning">Pickup<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "2":
                            $nestedData['delivery_status'] = '<div class="badge badge-info">Sent<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "3":
                            $nestedData['delivery_status'] = '<div class="badge badge-primary">Distribution<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "4":
                            $nestedData['delivery_status'] = '<div class="badge badge-success">Delivered<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "5":
                            $nestedData['delivery_status'] = '<div class="badge badge-danger">Ne répond pas<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "6":
                            $nestedData['delivery_status'] = '<div class="badge badge-danger">Injoignable<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "7":
                            $nestedData['delivery_status'] = '<div class="badge badge-danger">Erreur numéro<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "8":
                            $nestedData['delivery_status'] = '<div class="badge badge-danger">Reporté<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "9":
                            $nestedData['delivery_status'] = '<div class="badge badge-danger">Programmé<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "10":
                            $nestedData['delivery_status'] = '<div class="badge badge-danger">Annulé<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "11":
                            $nestedData['delivery_status'] = '<div class="badge badge-danger">Refusé<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                        case "12":
                            $nestedData['delivery_status'] = '<div class="badge badge-danger">Retourné<br>'.$lims_delivery_status_data->status_date.'</div>';
                            break;
                    }
                    $sale_delivery_status = $lims_delivery_status_data->status;
                    $sale_delivery_status_date = $lims_delivery_status_data->status_date;
                } else {
                    $nestedData['delivery_status'] = '<div class="badge badge-secondary">'.trans('file.Pas de livraison').'</div>';
                    $sale_delivery_status = "";
                    $sale_delivery_status_date = "";
                }
                $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle '.Auth::user()->role_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">';
                            if(Auth::user()->role_id == 1)
                            {
                                $nestedData['options'] .= '
                                <li>
                                    <a href="'.route('sale.invoice', $sale->id).'" class="btn btn-link">
                                    <i class="fa fa-copy"></i> '.trans('file.Generate Invoice').'</a>
                                </li>';
                            }
                            $nestedData['options'] .= '
                                <li>
                                    <button type="button" class="btn btn-link view"><i class="fa fa-eye"></i> '.trans('file.View').'</button>
                                </li>';
                if(in_array("sales-edit", $request['all_permission'])){
                    if($sale->is_valide != 1) {
                        $nestedData['options'] .= '<li>
                        <a href="'.route('sales.edit', $sale->id).'" class="btn btn-link"><i class="dripicons-document-edit"></i> '.trans('file.edit').'</a>
                        </li>'; 
                    } elseif(Auth::user()->role_id == 1) {
                        $nestedData['options'] .= 
                        '<li>
                            <a href="'.route('sales.edit', $sale->id).'" class="btn btn-link"><i class="dripicons-document-edit"></i> '.trans('file.edit').'</a>
                        </li>
                        <li>
                            <button type="button" class="add-delivery btn btn-link" data-id = "'.$sale->id.'"><i class="fa fa-truck"></i> '.trans('file.Add Delivery').'</button>
                        </li>
                        <li>
                            <button type="button" class="add-payment btn btn-link" data-id = "'.$sale->id.'" data-toggle="modal" data-target="#add-payment"><i class="fa fa-plus"></i> '.trans('file.Add Payment').'</button>
                        </li>
                        <li>
                            <button type="button" class="get-payment btn btn-link" data-id = "'.$sale->id.'"><i class="fa fa-money"></i> '.trans('file.View Payment').'</button>
                        </li>';
                    }
                }
                
                if(in_array("sales-delete", $request['all_permission']))
                    $nestedData['options'] .= \Form::open(["route" => ["sales.destroy", $sale->id], "method" => "DELETE"] ).'
                            <li>
                              <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> '.trans("file.delete").'</button> 
                            </li>'.\Form::close().'
                        </ul>
                    </div>';
                // data for sale details by one click
                $coupon = Coupon::find($sale->coupon_id);
                if($coupon)
                    $coupon_code = $coupon->code;
                else
                    $coupon_code = null;

                $nestedData['sale'] = array( '[ 
                    "'.date(config('date_format'), strtotime($sale->created_at->toDateString())).'"', //0
                    ' "'.$sale->reference_no.'"', //1
                    ' "'.$sale_status.'"', //2
                    ' "'.$sale->customer_name.'"', //3
                    ' "'.$sale->customer_tel.'"', //4
                    ' "'.$sale->customer_address.'"', //5
                    ' "'.$sale_city.'"', //6
                    ' "'.$sale_delivery_status.'"', //7
                    ' "'.$sale->user_id.'"', //8
                    ' "'.$sale->is_valide.'"', //9
                    ' "'.$sale->id.'"', //10
                    ' "'.$sale->total_price.'"', //11
                    ' "'.$sale->grand_total.'"', //12
                    ' "'.preg_replace('/[\n\r]/', "<br>", $sale->sale_note).'"', //13
                    ' "'.preg_replace('/[\n\r]/', "<br>", $sale->staff_note).'"', //14
                    ' "'.$request->input('search_string').'"', //15
                    ' "'.$request->input('starting_date').'"', //16
                    ' "'.$request->input('ending_date').'"', //17
                    ' "'.$status_id.'"', //18
                    ' "'.$condition.'"', //19
                    ' "'.$sale_delivery_status_date.'"]' //20
                );
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );

        echo json_encode($json_data);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'category' => 'required',
            'priority' => 'required',
            'message' => 'required'
        ]);

        $ticket_id = 'tck-' . strtolower(Auth::user()->name) . '-' . date("dmy") . '-'. date("His");

        $ticket = new Ticket([
            'title' => $request->input('title'),
            'user_id' => Auth::user()->id,
            'ticket_id' => $ticket_id,
            'category_id' => $request->input('category'),
            'priority' => $request->input('priority'),
            'message' => $request->input('message'),
            'status' => "Open"
        ]);
        $ticket->save();
        return redirect('tickets')->with('message', "A ticket with ID: #$ticket->ticket_id has been opened.");
    }

    public function userTickets()
    {
        $tickets = Ticket::where('user_id', Auth::user()->id)->paginate(10);
        return view('tickets.user_tickets', compact('tickets'));
    }
    
    public function show($id)
    {
        $ticket = Ticket::where('ticket_id', $id)->firstOrFail();
        return view('tickets.show', compact('ticket'));
    }

    public function close($ticket_id)
    {
        $ticket = Ticket::where('ticket_id', $ticket_id)->firstOrFail();
        $ticket->status = "Closed";
        $ticket->save();
        return redirect('tickets')->with('message', "The ticket with ID: #$ticket->ticket_id has been closed.");
    }
}