<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKasusPenyakit extends Model
{
    use HasFactory;
    protected $table = 'detail_kasus_penyakits';
    protected $fillable = [
        'tahun_id',
        'desa_id',
        'penyakit_id',
        'terjangkit',
        'meninggal',
    ];
    public function tahunKasus() {
        return $this->belongsTo(Tahun::class, 'tahun_id', 'id');
    }
    public function desaKasus() {
        return $this->belongsTo(Desa::class, 'desa_id', 'id');
    }
    public function penyakitKasus() {
        return $this->belongsTo(Penyakit::class, 'penyakit_id', 'id');
    }
}
