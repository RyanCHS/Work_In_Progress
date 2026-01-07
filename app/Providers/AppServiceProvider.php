<?php

namespace App\Providers;

use App\Implementations\UserService;

use App\Contracts\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

use App\Implementations\InspectionService;

use App\Implementations\NotificationService;

use App\Contracts\InspectionServiceInterface;
use App\Implementations\WorkingPermitService;
use App\Implementations\WorkInProgressService;
use App\Contracts\NotificationServiceInterface;
use App\Contracts\WorkingPermitServiceInterface;
use App\Contracts\WorkInProgressServiceInterface;

class AppServiceProvider extends ServiceProvider
{
  public function register(): void
  {
    $this->app->bind(InspectionServiceInterface::class, InspectionService::class);
    $this->app->bind(UserServiceInterface::class, UserService::class);
    $this->app->bind(WorkInProgressServiceInterface::class, WorkInProgressService::class);
    $this->app->bind(WorkingPermitServiceInterface::class, WorkingPermitService::class);
    $this->app->bind(NotificationServiceInterface::class, NotificationService::class);
  }

  public function boot(): void
  {
    //
  }
}
