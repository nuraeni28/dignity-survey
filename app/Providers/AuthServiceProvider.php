<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('owner', function ($user) {
            return $user->hasRole('owner') ? true : false;
        });
        Gate::define('koordinator-area', function ($user) {
            return $user->hasRole('koordinator-area') ? true : false;
        });

        Gate::define('super-admin', function ($user) {
            return $user->hasRole('super-admin') ? true : false;
        });

        Gate::define('admin', function ($user) {
            return $user->hasRole('admin') ? true : false;
        });

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage())
               ->subject('Yth ' . $notifiable->name . ', Silahkan Verifikasi Akun Anda')
                ->line('Halo ' . $notifiable->name . '!')
                 ->line('Terima kasih telah mendaftarkan diri Anda menjadi relawan. Silahkan klik link berikut untuk bergabung ke grup WhatsApp Sobat AAB')
                ->action('Link Bergabung WhatsApp', $url);
        });
    }
}
