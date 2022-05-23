<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    /**
     * Users associated to this conversation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Messages in this conversation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
