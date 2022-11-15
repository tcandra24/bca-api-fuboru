<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ERP_Invoice extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'pgsql_erp';
    protected $table = 'masterjual';
}