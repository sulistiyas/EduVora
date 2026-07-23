<?php

namespace App\Providers;

use App\Repositories\Reports\Contracts\AttendanceReportRepositoryInterface;
use App\Repositories\Reports\Contracts\ScoreReportRepositoryInterface;
use App\Repositories\Reports\Teacher\AttendanceReportRepository;
use App\Repositories\Reports\Teacher\ScoreReportRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AttendanceReportRepositoryInterface::class,
            AttendanceReportRepository::class,
        );

        $this->app->bind(
            ScoreReportRepositoryInterface::class,
            ScoreReportRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
