<?php

namespace Quantimodo\Generator\Generators\API;

use Config;
use Quantimodo\Generator\CommandData;
use Quantimodo\Generator\Generators\GeneratorProvider;
use Quantimodo\Generator\Utils\GeneratorUtils;
use Quantimodo\Generator\Utils\SwaggerTemplateUtil;

class APIControllerGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string  */
    private $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_api_controller', app_path('Http/Controllers/'));
    }

    public function generate()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('Controller', 'api');

        $templateData = $this->fillTemplate($templateData);

        $fileName = $this->commandData->modelName.'Controller.php';

        if (!file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nAPI Controller created: ");
        $this->commandData->commandObj->info($fileName);
    }

    public function fillTemplate($templateData)
    {
        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $fieldTypes = $this->commandData->getSwaggerTypes();

        $fieldTypes = array_merge($fieldTypes, [
             [
                'name' => "limit",
                'type' => "integer",
                "description" => ""
             ],
             [
                'name' => "offset",
                'type' => "integer",
                "description" => ""
             ],
             [
                'name' => "sort",
                'type' => "string",
                "description" => ""
             ]
        ]);

        $templateData = str_replace('$PARAMETERS$', implode(",\n", $this->generateSwagger($fieldTypes)), $templateData);

        return $templateData;
    }

    public function generateSwagger($fields)
    {
        $parameterTemplate = $this->commandData->templatesHelper->getTemplate("Parameter", 'swagger');

        $parameters = SwaggerTemplateUtil::prepareIndexParameters($parameterTemplate, $fields);

        return $parameters;
    }
}
