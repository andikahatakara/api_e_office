<?php

namespace App\Models;

use App\Traits\UseEmployee;
use App\Traits\UseFilterAble;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class IncomingLetter extends Model
{
    use HasFactory, UseFilterAble, UseEmployee;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'input_by',
        'to',
        'from',
        'number',
        'date',
        'about',
        'characteristic',
        'file'
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date:Y-m-d',
        'to' => 'integer',
        'input_by' => 'integer'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['actions'];

    /**
     * Retrive incoming letter inputed by
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function by() : BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by', 'id');
    }

    /**
     * Retrive incoming letter inputed by
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class, 'to', 'user_id');
    }

    /**
     * get all dispositions
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function dispositions() : HasMany
    {
        return $this->hasMany(Disposition::class);
    }

    /**
     * Get last disposition
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function disposition(): HasOne
    {
        return $this->hasOne(Disposition::class)->latestOfMany();
    }


    /**
     * Set actions attributes
    */
    public function actions() : Attribute
    {
        // $employee = User;
        return new Attribute(
            get: fn () => [
                [
                    'type' => 'disposition',
                    'action' => 'incoming-letters/disposition/'.$this->attributes['id'],
                    'can' => 'incoming-letters.disposition',
                    'useModal' => true,
                    'isHead' => $this->loginAsHeadOfDepartment(),
                ],
                [
                    'type' => 'update',
                    'action' => 'incoming-letters/edit/'.$this->attributes['id'],
                    'can' => 'incoming-letters.update',
                ],
                [
                    'type' => 'delete',
                    'action' => 'incoming_letters/'.$this->attributes['id'],
                    'can' => 'incoming-letters.destroy',
                    'useModal' => true,
                ],
            ]
        );
    }

    /**
     * Set full file url
    */
    public function fileUrl() : Attribute
    {
        return new Attribute(
            get: fn () => asset(Storage::url($this->attributes['file']))
        );
    }
}
