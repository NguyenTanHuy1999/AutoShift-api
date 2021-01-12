<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function register()
    {
    	// Transformer 
        $this->commands('Prettus\Repository\Generators\Commands\TransformerCommand');        
        
        // Criteria
        $this->commands('Prettus\Repository\Generators\Commands\CriteriaCommand');    
        // $this->commands('Prettus\Repository\Generators\Commands\EntityCommand');    
        // $this->commands('Prettus\Repository\Generators\Commands\PresenterCommand');    
        // $this->commands('Prettus\Repository\Generators\Commands\ControllerCommand');    
        // $this->commands('Prettus\Repository\Generators\Commands\ValidatorCommand');    
        // $this->commands('Prettus\Repository\Generators\Commands\BindingsCommand');    

         // Make Repository MongoDB
        $this->commands('App\Generators\Commands\RepositoryMongodbCommand');

        // Make Mail
        $this->commands('App\Generators\Commands\MailMakeCommand');  

        // Make Notification
        $this->commands('App\Generators\Commands\NotificationMakeCommand');    

        // Make Job
        $this->commands('App\Generators\Commands\JobMakeCommand'); 

        // Make Schedule
        $this->commands('App\Generators\Commands\ScheduleMakeCommand'); 
    }
}
