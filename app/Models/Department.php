<?php

namespace App\Models;

use App\Traits\UseCheckRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Department extends Model
{
    use HasFactory, UseCheckRole;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'level'
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
     * set attribute actions
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
    */
    public function actions() : Attribute
    {
        return new Attribute(
            get: fn () => [
                [
                    'type' => 'update',
                    'action' => $this->attributes['id'],
                    'useModal' => true,
                    'disabled' => ($this->attributes['id'] < 7 && true) ?? !$this->isSuperAdmin(),
                    'can' => 'departments.update'
                ],
                [
                    'type' => 'delete',
                    'action' => 'departments/'.$this->attributes['id'],
                    'useModal' => true,
                    'disabled' => ($this->attributes['id'] < 7 && true) ?? !$this->isSuperAdmin(),
                    'can' => 'departments.destroy'
                ],
            ]
        );
    }

    /**
     * Get the department employees
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
    */
    public function employees() : MorphMany
    {
        return $this->morphMany(Employee::class, 'employeeable');
    }

    /**
     * Get the department employees
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
    */
    public function employee() : MorphOne
    {
        return $this->morphOne(Employee::class, 'employeeable')->ofMany(['created_at' => 'max'], function(Builder $builder) {
            $builder->where('is_head', true);
        });
    }

    /**
     * Retriver all sub departments
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function subs() : HasMany
    {
        return $this->hasMany(SubDepartment::class);
    }
}
