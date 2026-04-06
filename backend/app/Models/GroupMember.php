<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    /** @use HasFactory<\Database\Factories\GroupMemberFactory>
     * HasUUids: Autmatically generates UUIDs for 'id' field.
     */
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable
     * 
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'group_id',
        'role_id'
    ];

    
}
