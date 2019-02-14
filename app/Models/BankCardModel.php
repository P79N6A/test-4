<?php
namespace App\Models;

class BankCardModel extends Model{
	protected $table = 'doc_bank_card';
	protected $primaryKey = 'id';

	protected $dateFormat = 'U';
}