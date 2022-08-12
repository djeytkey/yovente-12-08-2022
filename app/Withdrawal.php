<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable =[

        "reference_no", "user_id", "withdraw_amount", "withdraw_note", "is_valide"
    ];
}
