<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;
    protected $table = 'desas';
    protected $fillable = [
        'id',
        'kecamatan_id',
        'nama_desa',
    ];

    public function kecamatanDesa() {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }
    public function desaKasus() {
        return $this->hasMany(DetailKasusPenyakit::class, 'desa_id', 'id');
    }
}
