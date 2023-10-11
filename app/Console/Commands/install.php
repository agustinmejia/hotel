<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
use TCG\Voyager\VoyagerServiceProvider;
use Illuminate\Support\Facades\DB;

class install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hotel:install {--r|reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Administrador de Hotel';

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
     * @return int
     */
    public function handle()
    {
        $empty_database = false;
        try {
            DB::table('users')->first();
        } catch (\Throwable $th) {
            $empty_database = true;
        }
        if($empty_database  || $this->option('reset')){
            $this->call('key:generate');
            $this->call('migrate:fresh');
            $this->call('db:seed', ['--class' => 'TemplateSeeder']);
            $this->call('storage:link');
            $this->call('vendor:publish', ['--provider' => VoyagerServiceProvider::class, '--tag' => ['config', 'voyager_avatar']]);
            $this->info('Gracias por instalar Administrador de Hotel');
        }else{
            $this->error('Ya se encuentra instalado');
        }
    }
}
