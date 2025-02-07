<?php

namespace App\Models;

use App\Traits\UseFilterAble;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OutgoingLetter extends Model
{
    use HasFactory, UseFilterAble;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number',
        'date',
        'about',
        'characteristic',
        'file'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'actions',
        'file_url'
    ];

    /**
     * Set action attribute
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
    */
    protected function actions() : Attribute
    {
        return new Attribute(
            get: fn () => [
                [
                    'type' => 'show',
                    'action' => 'outgoing-letters/'.$this->attributes['id'],
                    'can' => 'outgoing-letters.show',
                    'useModal' => true
                ],
                [
                    'type' => 'update',
                    'action' => 'outgoing-letters/edit/'.$this->attributes['id'],
                    'can' => 'outgoing-letters.update'
                ],
                [
                    'type' => 'delete',
                    'action' => 'outgoing_letters/'.$this->attributes['id'],
                    'can' => 'outgoing-letters.destroy',
                    'useModal' => true
                ],
            ]
        );
    }

    /**
     * Set action attribute
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
    */
    protected function fileUrl() : Attribute
    {
        return new Attribute(get: fn () => asset(Storage::url($this->attributes['file'])));
    }
}
