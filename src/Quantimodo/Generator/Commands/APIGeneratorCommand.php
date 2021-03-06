<?php

namespace Quantimodo\Generator\Commands;

use Quantimodo\Generator\CommandData;
use Quantimodo\Generator\Generators\API\APIControllerGenerator;
use Quantimodo\Generator\Generators\Common\MigrationGenerator;
use Quantimodo\Generator\Generators\Common\ModelGenerator;
use Quantimodo\Generator\Generators\Common\RequestGenerator;
use Quantimodo\Generator\Generators\Common\RoutesGenerator;
use Quantimodo\Generator\Generators\Common\ServiceGenerator;

class APIGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'quantimodo.generator:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a full CRUD API for given model';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_API);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        if (!$this->commandData->skipMigration and !$this->commandData->fromTable) {
            $migrationGenerator = new MigrationGenerator($this->commandData);
            $migrationGenerator->generate();
        }

        $modelGenerator = new ModelGenerator($this->commandData);
        $modelGenerator->generate();

        $requestGenerator = new RequestGenerator($this->commandData);
        $requestGenerator->generate();

        $serviceGenerator = new ServiceGenerator($this->commandData);
        $serviceGenerator->generate();

        $controllerGenerator = new APIControllerGenerator($this->commandData);
        $controllerGenerator->generate();

        $routeGenerator = new RoutesGenerator($this->commandData);
        $routeGenerator->generate();

        if (!$this->commandData->skipMigration and !$this->commandData->fromTable) {
            if ($this->confirm("\nDo you want to migrate database? [y|N]", false)) {
                $this->call('migrate');
            }
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), []);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge(parent::getArguments(), []);
    }
}
