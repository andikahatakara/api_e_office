<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disposition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'incoming_letter_id',
        'from',
        'to',
        'note',
        'read_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'from' => 'integer',
        'to' => 'integer',
        'incoming_letter_id' => 'integer'
    ];

    /**
     * retrive employee receive the disposition
    */
    public function employeeTo() : BelongsTo
    {
        return $this->belongsTo(Employee::class, 'to', 'user_id');
    }

    /**
     * retrive employee give the disposition
    */
    public function employeeFrom() : BelongsTo
    {
        return $this->belongsTo(Employee::class, 'from', 'user_id');
    }

    /**
     * retrive incoming letter
    */
    public function incoming() : BelongsTo
    {
        return $this->belongsTo(IncomingLetter::class,'incoming_letter_id');
    }
}
