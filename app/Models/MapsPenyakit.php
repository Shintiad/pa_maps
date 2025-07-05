<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapsPenyakit extends Model
{
    use HasFactory;
    protected $table = 'maps_penyakits';
    protected $fillable = [
        'tahun_id',
        'penyakit_id',
        'link_metabase',
    ];
    public function tahunMaps() {
        return $this->belongsTo(Tahun::class, 'tahun_id', 'id');
    }
    public function penyakitMaps() {
        return $this->belongsTo(Penyakit::class, 'penyakit_id', 'id');
    }
}
