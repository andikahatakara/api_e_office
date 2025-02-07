<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class SubDepartment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug'
    ];

    /**
     * Get the employee of sub department
     * @return Illuminate\Database\Eloquent\Relations\MorphOne
    */
    public function employee() : MorphOne
    {
        return $this->morphOne(Employee::class, 'employeeable');
    }

    /**
     * Retrive department information from sub department
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function department() : BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
