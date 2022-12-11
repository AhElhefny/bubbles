<?php

namespace App\Console\Commands;

use App\Classes\Operation;
use App\Models\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AddCashback extends Command
{
    
    protected $signature = 'mawared:add-cashback';
    
    protected $description = 'Mawared add users cashback';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
       
    }
}
