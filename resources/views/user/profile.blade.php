@extends('layout.main')
@section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
@if(session()->has('message1'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message1') }}</div> 
@endif
@if(session()->has('message2'))
        <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message2') }}</div> 
@endif
@if(session()->has('message3'))
        <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('message3') }}</div> 
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Update User Profile')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => ['user.profileUpdate', Auth::id()], 'method' => 'put']) !!}
                        <input type="hidden" name="role_id" id="role_id" value="{{ Auth::user()->role_id }}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{trans('file.LastName')}} *</strong> </label>
                                            <input type="text" name="last_name" value="{{$lims_user_data->last_name}}" required class="form-control" />
                                            @if($errors->has('last_name'))
                                            <span>
                                               <strong class="has-error">{{ $errors->first('last_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{trans('file.FirstName')}} *</strong> </label>
                                            <input type="text" name="first_name" value="{{$lims_user_data->first_name}}" required class="form-control" />
                                            @if($errors->has('first_name'))
                                            <span>
                                               <strong class="has-error">{{ $errors->first('first_name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{trans('file.UserName')}} *</strong> </label>
                                            <input type="text" name="name" value="{{$lims_user_data->name}}" required class="form-control" />
                                            @if($errors->has('name'))
                                            <span>
                                               <strong class="has-error">{{ $errors->first('name') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{trans('file.Email')}} *</strong> </label>
                                            <input type="email" name="email" value="{{$lims_user_data->email}}" required class="form-control">
                                            @if($errors->has('email'))
                                            <span>
                                               <strong class="has-error">{{ $errors->first('email') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{trans('file.Phone Number')}} *</strong> </label>
                                            <input type="text" name="phone" value="{{$lims_user_data->phone}}" required class="form-control" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{trans('file.Company Name')}}</strong> </label>
                                            <input type="text" name="company_name" value="{{$lims_user_data->company_name}}" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.Bank Name')}} *</strong> </label>
                                    <input type="text" id="bank_name" name="bank_name" value="{{$lims_user_data->bank_name}}" required class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.RIB')}} *</strong> </label>
                                    <input type="number" id="rib" name="rib" value="{{$lims_user_data->rib}}" required class="form-control" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Change Password')}}</h4>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['route' => ['user.password', Auth::id()], 'method' => 'put']) !!}
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{trans('file.Current Password')}} *</strong> </label>
                                    <input type="password" name="current_pass" required class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.New Password')}} *</strong> </label>
                                    <input type="password" name="new_pass" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>{{trans('file.Confirm Password')}} *</strong> </label>
                                    <input type="password" name="confirm_pass" id="confirm_pass" required class="form-control">
                                </div>
                                <div class="form-group">
                                    <div class="registrationFormAlert" id="divCheckPasswordMatch">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">

    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #user-menu").addClass("active");

    $(document).ready(function() {
        var rib  = $('#rib').val(),
            bank = $('#bank_name').val(),
            role = $('#role_id').val()
        
        if ( rib !== "" ) {
            if ( role == 1 ) {
                $('#bank_name').prop('required',false);
                $('#rib').prop('required',false);
            } else {
                $('#bank_name').prop('disabled',true);
                $('#rib').prop('disabled',true);
            }
        }
    });

    $('#confirm_pass').on('input', function(){

        if($('input[name="new_pass"]').val() != $('input[name="confirm_pass"]').val())
            $("#divCheckPasswordMatch").html("Password doesn't match!");
        else
            $("#divCheckPasswordMatch").html("Password matches!");
         
    });
</script>
@endsection
