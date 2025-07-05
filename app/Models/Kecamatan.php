<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;
    protected $table = 'kecamatans';
    protected $fillable = [
        'id',
        'nama_kecamatan',
    ];

    public function kecamatanPenduduk()
    {
        return $this->hasMany(Penduduk::class, 'kecamatan_id', 'id');
    }
    public function kecamatanKasus()
    {
        return $this->hasMany(KasusPenyakit::class, 'kecamatan_id', 'id');
    }
    public function kecamatanDesa()
    {
        return $this->hasMany(Desa::class, 'kecamatan_id', 'id');
    }
    public function detailKasusPenyakit()
    {
        return $this->hasManyThrough(DetailKasusPenyakit::class, Desa::class, 'kecamatan_id', 'desa_id');
    }
    public function kecamatanDetailMaps()
    {
        return $this->hasMany(DetailMapsPenyakit::class, 'kecamatan_id', 'id');
    }
}
