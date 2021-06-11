<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'company_id'
    ];
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $url;
    /**
     * @var array
     */
    private $products;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var string
     */
    private $company_id;

}
