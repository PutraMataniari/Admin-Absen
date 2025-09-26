<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;

class BrevoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Extend mail.manager agar Laravel bisa membuat transport 'brevo'
        // $this->app->make('mail.manager')->extend('brevo', function () {
        //     $factory = new BrevoTransportFactory();
        //     // gunakan API transport (brevo+api://KEY@default)
        //     return $factory->create(
        //         new Dsn('brevo+api', 'default', env('BREVO_API_KEY'))
        //     );
        // });
    }
}
