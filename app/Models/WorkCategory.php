<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WorkCategory extends Model {
    protected $fillable = ['parent_id','name','level','base_price','active','sort_order'];
    protected $casts = ['active' => 'boolean', 'base_price' => 'decimal:2'];
    public function parent() { return $this->belongsTo(WorkCategory::class, 'parent_id'); }
    public function children() { return $this->hasMany(WorkCategory::class, 'parent_id')->where('active', true)->orderBy('sort_order'); }
}
