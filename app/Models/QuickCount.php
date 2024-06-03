<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickCount extends Model
{
    use HasFactory;
    protected $table = 'quick_count';
    protected $fillable = ['tps', 'jumlah_suara', 'foto', 'indonesia_province_id', 'indonesia_city_id', 'indonesia_district_id', 'indonesia_village_id', 'caleg_id', 'partai_id'];

   public function city()
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\City::class, 'indonesia_city_id');
    }
     public function caleg()
    {
        return $this->belongsTo(Caleg::class, 'caleg_id');
    }

       public function partai()
    {
        return $this->belongsTo(Partai::class, 'partai_id');
    }

    /**
     * Get the user district.
     */
    public function district()
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\District::class, 'indonesia_district_id');
    }

    /**
     * Get the user village.
     */
    public function village()
    {
        return $this->belongsTo(\Laravolt\Indonesia\Models\Village::class, 'indonesia_village_id');
    }

}
