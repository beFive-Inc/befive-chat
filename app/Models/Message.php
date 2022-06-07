<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chatroom_user_id',
        'message_id',
        'message'
    ];

    /**
     * @return string
     */
    public function getDateAttribute(): string
    {
        $date = Carbon::parse($this->created_at);
        if (Carbon::now()->diffInMinutes($this->created_at) < 60 || Carbon::now()->diffInHours($this->created_at) < 24) {
            return $date->format('H:i');
        } elseif (Carbon::now()->diffInDays() < 7) {
            return $date->format('D');
        } elseif(Carbon::now()->diffInWeeks() < 4) {
            return $date->format('d m');
        }

        return Carbon::parse($this->created_at)
            ->diffForHumans();
    }

    public function getDecryptedMessageAttribute()
    {
        return Crypt::decrypt($this->message);
    }

    /**
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(ChatroomUser::class, 'chatroom_user_id', 'id');
    }


    public function chatroom()
    {
        return $this->author()->with('chatroom');
    }
}