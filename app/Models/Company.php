<?php


namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'admin_id',
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $admin_id;
}
