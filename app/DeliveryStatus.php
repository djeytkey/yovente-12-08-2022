<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryStatus extends Model
{
    protected $fillable =[
        "reference_no", "status", "status_date"
    ];
}
