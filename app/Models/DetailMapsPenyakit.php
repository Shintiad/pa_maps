<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailMapsPenyakit extends Model
{
    use HasFactory;
    protected $table = 'detail_maps_penyakits';
    protected $fillable = [
        'tahun_id',
        'kecamatan_id',
        'penyakit_id',
        'link_metabase',
    ];
    public function tahunMaps() {
        return $this->belongsTo(Tahun::class, 'tahun_id', 'id');
    }
    public function kecamatanMaps() {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }
    public function penyakitMaps() {
        return $this->belongsTo(Penyakit::class, 'penyakit_id', 'id');
    }
}
