<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'taikhoan';
    protected $primaryKey = 'tendangnhap';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tendangnhap',
        'matkhau',
        'hoten',
        'quyen'
    ];

    protected $hidden = ['matkhau'];

    public $timestamps = false;

    public function getAuthPassword()
    {
        return $this->matkhau;
    }

    public function getAuthIdentifierName()
    {
        return 'tendangnhap';
    }
}