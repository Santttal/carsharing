<?php

namespace App\Console\Commands;

use App\Lib\Company;
use App\Lib\Delimobil;
use App\Lib\Telegram;
use App\Lib\Youdrive;
use App\Models\CarsharingPolygon;
use App\Models\CarStorage;
use App\Models\Filters;
use App\Models\Radius;
use App\Models\State;
use App\Models\SyncLog;
use Carbon\Carbon;
use Geokit\Math;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncCars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cars:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize carsharing items';
    /**
     * @var Company[]
     */
    private $companies;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->companies = [
            new Delimobil(),
            new Youdrive(),
        ];

        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->handleWithException();
        } catch (\Exception $e) {
            $log = new SyncLog();
            $log->level = SyncLog::LEVEL_FAILED;
            $log->message = Carbon::now()->setTimezone('Europe/Moscow') . ' ' . $e->getMessage();
            \App\Models\SyncLog::add($log);
            throw new $e;
        }

    }

    private function handleWithException()
    {
        Log::channel('single')->info('Started!');

        if (!State::get()) {
            Log::channel('single')->info('disabled');
            return true;
        }

        $filters = Filters::load();

        sleep(mt_rand(1, 15));
        $math = new Math();
        /** @var CarsharingPolygon[] $polygons */
        $polygons = CarsharingPolygon::where('state', true)->get();
        $radius = Radius::load();
        $telegram = new Telegram();


        /** @var Company[] $companies */
        $this->companies = [
            new Delimobil(),
            new Youdrive(),
        ];

        $allCars = [];
        foreach ($this->companies as $company) {
            $cars = $company->loadCars();
            $allCars = array_merge($allCars, $cars);
            foreach ($cars as $car) {
                $distanceFromMe = round($math->distanceHaversine($radius->coordinates, $car->getCoordinates())->meters(), 2);
                foreach ($polygons as $polygon) {
                    if ($polygon->coordinates->contains($car->getCoordinates()) && $filters->fits($car)) {
                        $company->makeOrder($car);
                        $telegram->sendMessage($company->getName() . ". Есть машина в {$polygon->name}, расстояние $distanceFromMe м");
                    }
                }
                if ($radius->state && $distanceFromMe < $radius->amount && $filters->fits($car)) {
                    $telegram->sendMessage($company->getName() . ". Есть машина в радиусе, расстояние $distanceFromMe м");
                }
            }
        }
        CarStorage::save($allCars);

        $log = new SyncLog();
        $log->level = SyncLog::LEVEL_SUCCESS;
        $log->message = Carbon::now()->setTimezone('Europe/Moscow') . ' Success';
        \App\Models\SyncLog::add($log);

        return true;
    }
}
