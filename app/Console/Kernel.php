<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Temp::class,
        \App\Console\ExtracResponseTbo::class,
        \App\Console\InsertAvilableTbo::class,
        \App\Console\GetClientRefrenceId::class,
        \App\Console\SessionCheck::class,
        \App\Console\GetPartoSearchResult::class,
        \App\Console\GetPartoBookingData::class,
        \App\Console\TboHotelDetail::class,
        \App\Console\GetItoursPnarBookResponse::class,
        \App\Console\GetItoursPnrReserveRequests::class,
        \App\Console\GetItoursFlightReserveResponse::class,
        \App\Console\GetItoursFlightReserveRequest::class,
        \App\Console\GetPnrDetailsRequest::class,
        \App\Console\GetPnrDetailsResponse::class,
        \App\Console\ConfirmByDepositResponse::class,
        \App\Console\GetDirectTicketById::class,
        \App\Console\GetAuthResponse::class,
        \App\Console\GetIssuePnrItoursResponse::class,
        \App\Console\GetIssuePnrItoursRequest::class,
        \App\Console\GetPricePnrItoursRequest::class,
        \App\Console\GetPricePnrItoursResponse::class,
        \App\Console\GetNewItoursHeader::class,
        \App\Console\GetPartoHotelSearchResult::class,
        \App\Console\GetPartoHotelPricing::class,
        \App\Console\GetPartoHotelBooking::class,
        \App\Console\GetPartoHotelDetail::class,
        \App\Console\AccelaeroSearchSampleResponse::class,
        \App\Console\AccelaeroPrice::class,
        \App\Console\CityNetSearchResponse::class,
        \App\Console\CityNetRules::class,
        \App\Console\SepehrSearchResponses::class,
        \App\Console\AmadeusNewGetSearchResponses::class,
        \App\Console\AmadeusNewFareRulesResponses::class,
        \App\Console\AmadeusNewGetPricePNRResponses::class,
        \App\Console\AmadeusNewPnrRetrieveResponses::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(SessionCheck::class)->everyMinute();
    }
}
