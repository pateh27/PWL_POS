<?php
 
 namespace App\Models;
 
 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\Relations\BelongsTo;
 use Illuminate\Database\Eloquent\Relations\HasMany;
 
 class PenjualanModel extends Model
 {
     use HasFactory;
     protected $table = 't_penjualan';
     protected $primaryKey = 'penjualan_id';
     protected $fillable = [
        'user_id', 
        'pembeli', 
        'penjualan_kode', 
        'penjualan_tanggal', 
        'created_at', 
        'updated_at'];
 
     public function user(): BelongsTo 
     {
         return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
     }
 
     public function details(): HasMany
     {
         return $this->hasMany(PenjualanDetailModel::class, 'penjualan_id', 'penjualan_id');
     }
 
     // code function untuk mengenerate kode transaksi
     public static function generateKode()
     {
         $prefix = 'TRX';
         $date = date('Ymd');
         $lastTransaction = self::whereDate('created_at', date('Y-m-d'))
             ->orderBy('penjualan_id', 'desc')
             ->first();
         
         $number = 1;
         if ($lastTransaction) {
             $lastCode = $lastTransaction->penjualan_kode;
             $lastNumber = (int) substr($lastCode, -4);
             $number = $lastNumber + 1;
         }
         
         return $prefix . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
     }
 
     // code function untuk menghitung total transaksi
     public function getTotalAmount()
     {
         return $this->details->sum(function($detail) {
             return $detail->jumlah * $detail->harga;
         });
     }
 }