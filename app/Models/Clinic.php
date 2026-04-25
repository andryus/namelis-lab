<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Clinic extends Model {
    protected $fillable = ['user_id','name','nif','address','city','phone','doctor_name','license_number','approved'];
    protected $casts = ['approved' => 'boolean'];
    public function user() { return $this->belongsTo(User::class); }
    public function orders() { return $this->hasMany(Order::class); }
}
