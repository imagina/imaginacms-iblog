<?php

namespace Modules\Iblog\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Isite\Entities\Event;



class EventTableSeeder extends Seeder
{
    public $events = [
        "PostWasCreated",
        "PostWasUpated",
        "PostWasDeleted",
        "CategoryWasCreated",
        "CategoryWasUpated",
        "CategoryWasDeleted",
        
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

       foreach ($this->events as $event) {
            
            $dbEvent = Event::where("module_name", "Iblog")
            ->where("name", $event)
            ->first();
            
            if(empty($dbEvent)){
                Event::create([
                    "module_name" => "Iblog",
                    "name" => $event
                ]);

            }
        }
        
    }
}
