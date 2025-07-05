<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tahun extends Model
{
    use HasFactory;
    protected $table = 'tahuns';
    protected $fillable = [
        'id',
        'tahun',
        'link_metabase',
    ];
    public function tahunPenduduk() {
        return $this->hasMany(Penduduk::class, 'tahun_id', 'id');
    }
    public function tahunPenyakit() {
        return $this->hasMany(KasusPenyakit::class, 'tahun_id', 'id');
    }
    public function tahunMaps() {
        return $this->hasMany(MapsPenyakit::class, 'tahun_id', 'id');
    }
    public function tahunDetailPenyakit() {
        return $this->hasMany(DetailKasusPenyakit::class, 'tahun_id', 'id');
    }
    public function tahunDetailMaps() {
        return $this->hasMany(DetailMapsPenyakit::class, 'tahun_id', 'id');
    }
}
