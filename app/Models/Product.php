<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    // use SoftDeletes;
    
   /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';
    /**
    * The primary key associated with the table.
    *
    * @var string
    */
   // protected $primaryKey = 'id';


   protected $fillable = [
     'id',
     'shop_id',
     'name',
     'price',
     'stock',
     'video',
   ];
}
