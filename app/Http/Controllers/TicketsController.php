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
            4 => 'priority',
            6 => 'status',
        );

        if($request->input('status_id') != 2)
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
            $totalData = Ticket::where([
                                            ['user_id', Auth::id()],
                                            ['status', $status_id]
                                        ])
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->count();
        elseif(Auth::user()->role_id > 2 && config('staff_access') == 'own')
            $totalData = Ticket::where('user_id', Auth::id())
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->count();
        elseif($status_id != 2)
            $totalData = Ticket::where('status', $status_id)
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->count();
        else
            $totalData = Ticket::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->count();

        $totalFiltered = $totalData;
        if($request->input('length') != -1)
            $limit = $request->input('length');
        else
            $limit = $totalData;
        $start = $request->input('start');
        $order = 'tickets.'.$columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $condition = "vide";
        if(empty($request->input('search_string'))) {
            if(Auth::user()->role_id > 2 && config('staff_access') == 'own' && $status_id != 2)
                $tickets = Ticket::where([
                                            ['user_id', Auth::id()],
                                            ['status', $status_id]
                                        ])
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            elseif(Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $tickets = Ticket::where('user_id', Auth::id())
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            elseif($status_id != 2)
                $tickets = Ticket::where('status', $status_id)
                        ->whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            else
                $tickets = Ticket::whereDate('created_at', '>=' ,$request->input('starting_date'))
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
                $tickets = Ticket::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['user_id', Auth::id()],
                            ['status', $status_id],
                            ['ticket_id', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['status', $status_id],
                            ['title', 'LIKE', "%{$search}%"]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                $totalFiltered = Ticket::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['user_id', Auth::id()],
                            ['status', $status_id],
                            ['ticket_id', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['is_valide', $status_id],
                            ['title', 'LIKE', "%{$search}%"]
                        ])
                        ->count();
            }                
            elseif(Auth::user()->role_id > 2 && config('staff_access') == 'own')
            {
                $condition = "Auth::user()->role_id > 2 && config('staff_access') == 'own'";
                $tickets = Ticket::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['user_id', Auth::id()],
                            ['ticket_id', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['title', 'LIKE', "%{$search}%"]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                $totalFiltered = Ticket::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['user_id', Auth::id()],
                            ['ticket_id', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['user_id', Auth::id()],
                            ['title', 'LIKE', "%{$search}%"]
                        ])
                        ->count();
            }                
            elseif($status_id != 2)
            {
                $condition = "status_id != 2";
                $tickets = Ticket::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['status', $status_id],
                            ['ticket_id', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['status', $status_id],
                            ['title', 'LIKE', "%{$search}%"]
                        ])
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                $totalFiltered = Ticket::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where([
                            ['status', $status_id],
                            ['ticket_id', 'LIKE', "%{$search}%"]
                        ])
                        ->orwhere([
                            ['status', $status_id],
                            ['title', 'LIKE', "%{$search}%"]
                        ])
                        ->count();
            }
            else
            {
                $condition = "else";
                $tickets = Ticket::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where('ticket_id', 'LIKE', "%{$search}%")
                        ->orwhere('title', 'LIKE', "%{$search}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();

                $totalFiltered = Ticket::whereDate('created_at', '>=' ,$request->input('starting_date'))
                        ->whereDate('created_at', '<=' ,$request->input('ending_date'))
                        ->where('ticket_id', 'LIKE', "%{$search}%")
                        ->orwhere('title', 'LIKE', "%{$search}%")
                        ->count();
            }
        }
        
        $data = array();
        if(!empty($tickets))
        {
            foreach ($tickets as $key=>$ticket)
            {
                $nestedData['id'] = $ticket->id;
                $nestedData['key'] = $key;
                $nestedData['date'] = date(config('date_format'), strtotime($ticket->created_at->toDateString()));
                $nestedData['ticket_id'] = $ticket->ticket_id;
                $nestedData['title'] = $ticket->title;

                if ($ticket->priority == "low") {
                    $nestedData['priority'] = '<div class="badge badge-info">'.trans('file.Low').'</div>';
                } elseif ($ticket->priority == "medium") {
                    $nestedData['priority'] = '<div class="badge badge-warning">'.trans('file.Medium').'</div>';
                } else {
                    $nestedData['priority'] = '<div class="badge badge-danger">'.trans('file.High').'</div>';
                }

                $nestedData['category'] = $ticket->category->name;                

                if($ticket->status == "1") {
                    $nestedData['status'] = '<div class="badge badge-success">'.trans('file.Opened').'</div>';
                    $nestedData['options'] = '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle '.Auth::user()->role_id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'.trans("file.action").'
                              <span class="caret"></span>
                              <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">';
                    $nestedData['options'] .= '
                                <li>
                                    <a href="'.url("tickets/" . $ticket->ticket_id).'" class="btn btn-primary">
                                    <i class="dripicons-document-edit"></i> '.trans('file.Comment').'</a>
                                </li>';
                    if(Auth::user()->role_id == 1)
                    {
                        $nestedData['options'] .= '
                        <li>
                            <form action="'.url("close_ticket/" . $ticket->ticket_id).'" method="POST">
                                <button type="submit" class="btn btn-danger">Close</button>
                            </form>
                        </li>';
                    }

                    $nestedData['options'] .= '</ul>';
                } else {
                    $nestedData['status'] = '<div class="badge badge-danger">'.trans('file.Closed').'</div>';
                    $nestedData['options'] = '---';
                }

                $nestedData['last_update'] = date(config('date_format'), strtotime($ticket->updated_at->toDateString()));

                
                            
                            
                
                // $nestedData['sale'] = array( '[ 
                //     "'.date(config('date_format'), strtotime($ticket->created_at->toDateString())).'"', //0
                //     ' "'.$ticket->reference_no.'"', //1
                //     ' "'.$sale_status.'"', //2
                //     ' "'.$ticket->customer_name.'"', //3
                //     ' "'.$ticket->customer_tel.'"', //4
                //     ' "'.$ticket->customer_address.'"', //5
                //     ' "'.$sale_city.'"', //6
                //     ' "'.$sale_delivery_status.'"', //7
                //     ' "'.$ticket->user_id.'"', //8
                //     ' "'.$ticket->is_valide.'"', //9
                //     ' "'.$ticket->id.'"', //10
                //     ' "'.$ticket->total_price.'"', //11
                //     ' "'.$ticket->grand_total.'"', //12
                //     ' "'.preg_replace('/[\n\r]/', "<br>", $ticket->sale_note).'"', //13
                //     ' "'.preg_replace('/[\n\r]/', "<br>", $ticket->staff_note).'"', //14
                //     ' "'.$request->input('search_string').'"', //15
                //     ' "'.$request->input('starting_date').'"', //16
                //     ' "'.$request->input('ending_date').'"', //17
                //     ' "'.$status_id.'"', //18
                //     ' "'.$condition.'"', //19
                //     ' "'.$sale_delivery_status_date.'"]' //20
                // );
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
            'status' => "1"
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
        $ticket->status = "0";
        $ticket->save();
        return redirect('tickets')->with('message', "The ticket with ID: #$ticket->ticket_id has been closed.");
    }
}