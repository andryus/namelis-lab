<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model {
    protected $fillable = ['order_id','tooth_fdi','work_category_id','vita_shade','mounting','finishing_stage','unit_price','notes'];
    public function order() { return $this->belongsTo(Order::class); }
    public function workCategory() { return $this->belongsTo(WorkCategory::class); }
}
