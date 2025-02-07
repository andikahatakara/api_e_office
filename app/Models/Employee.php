<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'is_head',
        'employeeable_id',
        'employeeable_type',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'actions'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'actions'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'employeeable_id' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * Get the parent employeeable model (department or sub department).
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function employeeable() : MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Retrive user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class,'user_id', 'id');
    }

    /**
     * Scope a query to only include employee isHead.
    */
    public function scopeHead(Builder $query) : Builder
    {
        return $query->where('is_head', true);
    }

    /**
     * Set attribute of actions
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
    */
    protected function actions() : Attribute
    {
        return new Attribute(
            get: fn () => [
                [
                    'type' => 'update',
                    'action' => 'employees/edit/'.$this->attributes['user_id'],
                    'can' => 'employees.update'
                ],
                [
                    'type' => 'delete',
                    'action' => 'employees/'.$this->attributes['user_id'],
                    'disabled' => $this->attributes['user_id'] === auth()->user()->id ? true : false,
                    'useModal' => true,
                    'can' => 'employees.destroy'
                ],
            ]
        );
    }
}
