<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ContainerStatus extends Model
{

    protected $table = 'container_statuses';


    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active'
    ];


    public function containers()
    {
        return $this->hasMany(
            Container::class,
            'current_status_id'
        );
    }

}