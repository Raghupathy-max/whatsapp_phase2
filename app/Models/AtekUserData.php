<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string cust_id
 */
class AtekUserData extends Model
{
    use HasFactory;

    // atek_user_datas

    protected $table = "atek_user_data";
    protected $primaryKey = "";
    public $timestamps = false;


}
