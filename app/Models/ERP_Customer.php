<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ERP_Customer extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $connection = 'pgsql_erp';
    protected $table = 'pelanggan';

    public function invoices()
    {
        return $this->hasMany(ERP_Invoice::class);
    }
}