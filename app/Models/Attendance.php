<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $casts = [
        'check_violation'   =>  'boolean'
    ];

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->beforeCreate();

            $model->checkViolation();
        });
    }

    protected function beforeCreate()
    {
        $user   =   auth()->user();

        if (!$user) abort(403, 'Unauthorized action.');

        $user_has_check_in  =   false;
        $user_has_check_out =   false;

        if ($user instanceof User) {

            $this->user_id  =   $user->id;

            $this->date = now()->format('Y-m-d');
            $this->time = now()->format('H:i:s');

            $user_has_check_in  =   $user->attendances()
                ->whereDate('created_at', now()->format('Y-m-d'))
                ->where('type', 'check_in')
                ->exists();

            $user_has_check_out =   $user->attendances()
                ->whereDate('created_at', now()->format('Y-m-d'))
                ->where('type', 'check_out')
                ->exists();


            if ($user_has_check_in && $user_has_check_out) {
                throw new \Exception('Pengguna sudah absen masuk & pulang hari ini.');
            } else if ($user_has_check_in) {
                $this->type =   'check_out';
            } else {
                $this->type =   'check_in';
            }
        }
    }
    protected function checkViolation()
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        if ($user instanceof User) {

            $this->violation_note = 'Tidak Ada Pelanggaran.';

            $time_in = Carbon::parse($this->location->time_in);
            $time_out = Carbon::parse($this->location->time_out);
            $allowance = $this->location->allowance; // in minutes
            $user_check_time = Carbon::now('Asia/Makassar');

            if ($this->type == 'check_in') {
                // Allowance time for check-in (time_in + allowance minutes)
                $allowance_time = $time_in->clone()->addMinutes($allowance);


                if ($user_check_time->isAfter($allowance_time)) {
                    $minutes_late = $user_check_time->diffInMinutes($time_in);
                    $this->check_violation = 1;
                    $this->violation_note = 'Terlambat ' . $minutes_late . ' menit.';
                }
            }

            if ($this->type == 'check_out') {
                // Allowance time for check-out (time_out - allowance minutes)
                $allowance_time_out = $time_out->clone()->subMinutes($allowance);

                if ($user_check_time->isBefore($allowance_time_out)) {
                    $minutes_early = $time_out->diffInMinutes($user_check_time);
                    $this->check_violation = 1;
                    $this->violation_note = 'Pulang cepat ' . $minutes_early . ' menit.';
                }
            }
        }
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(AttendanceLocation::class, 'location_id');
    }
}
