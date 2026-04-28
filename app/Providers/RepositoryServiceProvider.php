<?php

namespace App\Providers;

use App\Repositories\CourseRepository;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->bind(
            CourseRepositoryInterface::class,
            CourseRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
