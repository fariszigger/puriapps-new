<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'type', // Perorangan / Badan
        'identity_number', // KTP / NPWP
        'phone_number',
        'job',
        'user_id',

        // Personal
        'pob',
        'dob',
        'gender',
        'marital_status',
        'mother_name',
        'education',
        'emergency_contact',

        // Address & Location
        'address',
        'village',
        'district',
        'regency',
        'province',
        'latitude',
        'longitude',

        // Spouse
        'spouse_name',
        'spouse_identity_number',
        'spouse_pob',
        'spouse_dob',
        'spouse_relation',
        'spouse_description',
        'spouse_job',
        'spouse_education',
        'spouse_notelp',

        // Files
        'photo_path',
        'document_path',
        'location_image_path',
        'path_distance',
    ];

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customerVisits()
    {
        return $this->hasMany(CustomerVisit::class);
    }

    public function visits()
    {
        return $this->hasMany(CustomerVisit::class);
    }

    public function warningLetters()
    {
        return $this->hasMany(WarningLetter::class);
    }
}
