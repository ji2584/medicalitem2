<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'ADMIN';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = ['AdminID', 'AdminPass', 'AdminName', 'CreatedAt'];
}