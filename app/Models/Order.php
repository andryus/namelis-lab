<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends Model {
    use SoftDeletes;
    protected $fillable = ['reference','clinic_id','assigned_tech_id','patient_ref','impression_type','stl_url','stl_external_url','status','requested_delivery_date','clinical_notes','internal_notes','estimated_price','barcode','created_at'];
    protected $casts = ['requested_delivery_date' => 'date', 'estimated_price' => 'decimal:2'];
    public function clinic() { return $this->belongsTo(Clinic::class); }
    public function assignedTech() { return $this->belongsTo(User::class, 'assigned_tech_id'); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function messages() { return $this->hasMany(OrderMessage::class)->latest(); }
    public static function nextReference(): string {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->count();
        return 'NML-' . $year . '-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
