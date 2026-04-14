<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class SupportMessage extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'message',
        'attachment_path',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withoutGlobalScopes();
    }
}
