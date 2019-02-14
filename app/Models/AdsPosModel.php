<?php
namespace App\Models;
class AdsPosModel extends Model{
    public $timestamp = false;
    protected $table = 'ads_pos';
    protected $fillable = ['name'];

}
