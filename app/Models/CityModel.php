<?php
namespace App\Models;
class CityModel extends Model{
    public $timestamps = false;
    protected $table = 'city';
    protected $fillable = ['id','name','first_letter','is_hot'];


}
