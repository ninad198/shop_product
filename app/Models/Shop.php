<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    // use SoftDeletes;
   /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shops';
    /**
    * The primary key associated with the table.
    *
    * @var string
    */
   // protected $primaryKey = 'id';


   protected $fillable = [
     'id',
     'name',
     'email',
     'address',
     'image'
   ];
}
