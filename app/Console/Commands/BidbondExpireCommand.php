<?php


namespace App\Console\Commands;


use App\Jobs\ExpireBidbond;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;


class BidbondExpireCommand extends Command
{

    protected $signature = 'bidbond:expire';

    protected $description = 'This command expires all bid bonds that are past due date';


    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        Bus::dispatch(new ExpireBidbond());
    }
}
