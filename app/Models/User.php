<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;



class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen' => 'datetime',
        'password' => 'hashed',
    ];

    public function isOnline()
    {
        if (!$this->last_seen) {
            return false;
        }
        return $this->last_seen->isAfter(Carbon::now()->subMinutes(5));
    }

    public function updateProfilePhoto($photo)
    {
        $path = $photo->store('profile-photos', 'public');
        $this->forceFill([
            'profile_photo_path' => $path,
        ])->save();
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? Storage::url($this->profile_photo_path)
            : asset('default-avatar.jpg');
    }

    public function emotionAnalyses()
    {
        return $this->hasMany(EmotionAnalysis::class);
    }
}
