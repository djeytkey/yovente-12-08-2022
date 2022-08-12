<?php

namespace App\Http\Controllers;

use App\Withdrawal;
use App\GeneralSetting;
use App\Sale;
use App\Product_Sale;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Auth;
use DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $role = Role::find(Auth::user()->role_id);
        if ($role->hasPermissionTo('withdraw-index')) {
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission)
                $all_permission[] = $permission->name;
            if (empty($all_permission))
                $all_permission[] = 'dummy text';

            if (Auth::user()->role_id > 2 && config('staff_access') == 'own')
                $lims_withdraw_all = Withdrawal::where('user_id', Auth::id())->orderBy('id', 'desc')->get();
            else
                $lims_withdraw_all = Withdrawal::all();
            return view('withdraw.index', compact('lims_withdraw_all', 'all_permission'));
        } else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //dd($request);
        $data = $request->all();
        $general_settings = GeneralSetting::latest()->first();
        $min_withdraw = $general_settings->min_withdraw;
        if ($data['withdraw_amount'] < $min_withdraw) {
            return redirect()->back()->with('not_permitted', trans('file.Sorry! The minimum amount is not reached'));
        }
        if ($data['withdraw_available'] >= $data['withdraw_amount']) {
            $data['is_valide'] = 0;
            $data['is_paid'] = 0;
            Withdrawal::create($data);
            return redirect('withdraw')->with('message', 'Data inserted successfully');
        } else {
            return redirect()->back()->with('not_permitted', trans('file.Sorry! You have exceeded the available amount'));
        }        
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $role = Role::firstOrCreate(['id' => Auth::user()->role_id]);
        if ($role->hasPermissionTo('withdraw-edit'))
        {
            $lims_withdraw_data = Withdrawal::find($id);
            $retait_total = 0;
            $retait_total = Withdrawal::where('user_id', Auth::id())->sum('withdrawals.withdraw_amount');
            $user_sales = Sale::where('user_id', Auth::id())->get();
            if ($user_sales->count() > 0)
            {
                $grand_total = 0;
                $original_total = 0;
                $livraison_total = 0;
                $benifice = 0;
                foreach ($user_sales as $user_sale)
                {
                    $grand_total += $user_sale->grand_total;
                    $livraison_total += $user_sale->livraison;
                    $original_prices = Product_Sale::where('sale_id', $user_sale->id)->get();
                    foreach ($original_prices as $original_price)
                    {
                        $original_total += $original_price->original_price * $original_price->qty;
                    }
                }
                $benifice = $grand_total - $original_total - $livraison_total - $retait_total;
                $lims_withdraw_data['withdraw_available'] = $benifice;
            }
            return $lims_withdraw_data;
        } else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        //dd($request);
        $data = $request->all();
        $general_settings = GeneralSetting::latest()->first();
        $min_withdraw = $general_settings->min_withdraw;
        if ($data['withdraw_amount'] < $min_withdraw) {
            return redirect()->back()->with('not_permitted', trans('file.Sorry! The minimum amount is not reached'));
        }
        if ($data['withdraw_available'] >= $data['withdraw_amount']) {
            $lims_withdraw_data = Withdrawal::find($data['withdraw_id']);
            $lims_withdraw_data->update($data);
            return redirect('withdraw')->with('message', 'Data updated successfully');
        } else {
            return redirect()->back()->with('not_permitted', trans('file.Sorry! You have exceeded the available amount'));
        }
    }

    public function deleteBySelection(Request $request)
    {
        $withdraw_id = $request['withdrawIdArray'];
        foreach ($withdraw_id as $id) {
            $lims_withdraw_data = Withdrawal::find($id);
            $lims_withdraw_data->delete();
        }
        return 'Withdraw deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_withdraw_data = Withdrawal::find($id);
        $lims_withdraw_data->delete();
        return redirect('withdraw')->with('not_permitted', 'Data deleted successfully');
    }
}
