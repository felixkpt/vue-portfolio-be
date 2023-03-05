<?php

namespace App\Cih\Commands\Autogenerate;

use App\Cih\Commands\Autogenerate\Traits\CommonTrait;
use Illuminate\Console\Command;

class AutoGenerateList extends Command
{

    public static function commands()
    {
        return [
            self::class,
            Migration::class,
            Model::class,
            Controller::class,
            Route::class,
            View::class,
        ];
    }

    /**
     * Autogenerate common methods
     */
    use CommonTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autogenerate:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a model, migration, controller, route and view at the same time';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->alertUser("Hi! Welcome to autogenerate list.");
        while (1) {

            $option = $this->ask("What do you want to do?\n1. Create Migration\n2. Create Model\n3. Create Controller\n4. Create Route\n5. Create View\n6.Exit");
            if ($option == 1) $this->call('autogenerate:migration');
            elseif ($option == 2) $this->call('autogenerate:model');
            elseif ($option == 3) $this->call('autogenerate:controller');
            elseif ($option == 4) $this->call('autogenerate:route');
            elseif ($option == 5) $this->call('autogenerate:view');
            elseif ($option == 6) return $this->alertUser("Bye, Happy Coding!");
            else $this->error("Unknown option, please try again");
        }
    }
}
