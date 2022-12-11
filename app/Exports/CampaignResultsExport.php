<?php

namespace App\Exports;
use App\Models\PromocodeCity;
use App\Models\PromoCodeuser;
use App\User;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\UserBalance;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Events\BeforeSheet;
use DB;
use Maatwebsite\Excel\Concerns\Exportable;


class CampaignResultsExport implements FromCollection , WithEvents
{
    use Exportable;
    private $collection;

    public function __construct($arrays)
    {
        $output = [];

        foreach ($arrays as $array) {
            // get headers for current dataset
            
            if(!empty($array)){

             $output[] = array_keys($array[0]);

            }
            // store values for each row
            foreach ($array as $row) {
                $output[] = array_values($row);
            }
            // add an empty row before the next dataset
            $output[] = [''];
        }

        $this->collection = collect($output);
    }

    public function collection()
    {
        return $this->collection;
    }

    public function registerEvents(): array
    {
        return [

            BeforeSheet::class  =>function(BeforeSheet $event){
                $event->getDelegate()->setRightToLeft(true);
            }
        ];
    }
}
        

