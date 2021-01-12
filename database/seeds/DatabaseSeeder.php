<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->call('ImmunizationScheduleDocumentSeeder');
        //$this->call('WonderWeekDocumentSeeder');
        
        //$this->call('NewsSectionDocumentSeeder');
        //$this->call('NewsCategoryDocumentSeeder');
        //$this->call('NewsDocumentSeeder');
    }
}
