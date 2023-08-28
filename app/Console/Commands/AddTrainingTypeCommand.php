<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\TestCenter\Activities\TrainingTypeActivity;

class AddTrainingTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:training-type {name} {--description=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(TrainingTypeActivity $trainingTypeActivity)
    {
        $this->info("Adding training type");
        $name = $this->argument('name');

        if(!$name)
        {
            $this->error("Name is required to create training type");
            return 0;
        }

        if( $trainingTypeActivity->isNameExist($name) )
        {
            $this->error("$name already exist");
            return 0;
        }

        $data = [];
        $data['name'] = $name;
        $data['description'] = $this->option('description');

        
        if( $training_type = $trainingTypeActivity->createTrainingType($data) )
        {
            $this->info("Training type created successfully!");
            return 0;
        }

        $this->error("Something wennt wrong while trying to create training type");

        return 0;
    }
}
