<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasusPenyakit extends Model
{
    use HasFactory;
    protected $table = 'kasus_penyakits';
    protected $fillable = [
        'tahun_id',
        'kecamatan_id',
        'penyakit_id',
        'terjangkit',
        'meninggal',
    ];
    public function tahunKasus() {
        return $this->belongsTo(Tahun::class, 'tahun_id', 'id');
    }
    public function kecamatanKasus() {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }
    public function penyakitKasus() {
        return $this->belongsTo(Penyakit::class, 'penyakit_id', 'id');
    }
}
