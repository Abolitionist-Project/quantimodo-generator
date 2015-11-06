<?php

namespace Quantimodo\Generator\Generators\Common;

use Config;
use Quantimodo\Generator\CommandData;
use Quantimodo\Generator\Generators\GeneratorProvider;
use Quantimodo\Generator\Utils\GeneratorUtils;
use Quantimodo\Generator\Utils\SwaggerTemplateUtil;

class ModelGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_model', app_path('Models/'));
    }

    public function generate()
    {
        $templateName = 'Model';

        $templateData = $this->commandData->templatesHelper->getTemplate($templateName, 'common');

        $templateData = $this->fillTemplate($templateData);

        $fileName = $this->commandData->modelName . '.php';

        if (!file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }

        $path = $this->path . $fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nModel created: ");
        $this->commandData->commandObj->info($fileName);
    }

    private function fillTemplate($templateData)
    {
        if (!$this->commandData->useSoftDelete) {
            $templateData = str_replace('$SOFT_DELETE_IMPORT$', '', $templateData);
            $templateData = str_replace('$SOFT_DELETE$', '', $templateData);
            $templateData = str_replace('$SOFT_DELETE_DATES$', '', $templateData);
        }

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $fillables = [];

        foreach ($this->commandData->inputFields as $field) {
            if (in_array($field['fieldName'], $this->commandData->excludedFields) or $field['fieldName'] == "id") {
                continue;
            }
            $fillables[] = '"' . $field['fieldName'] . '"';
        }

        $templateData = str_replace('$FIELDS$', implode(",\n\t\t", $fillables), $templateData);

        $templateData = str_replace('$RULES$', implode(",\n\t\t", $this->generateRules()), $templateData);

        $castFields = $this->generateCasts();

        $templateData = str_replace('$CAST$', implode(",\n\t\t", $castFields), $templateData);

        $fieldTypes = $this->commandData->getSwaggerTypes();

        $templateData = str_replace('$SWAGGER_DOCS$', $this->generateSwagger($fieldTypes, $fillables), $templateData);

        return $templateData;
    }

    private function generateRules()
    {
        $rules = [];

        foreach ($this->commandData->inputFields as $field) {
            if (!empty($field['validations'])) {
                $rule = '"' . $field['fieldName'] . '" => "' . $field['validations'] . '"';
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    public function generateCasts()
    {
        $casts = [];

        foreach ($this->commandData->inputFields as $field) {
            if (in_array($field['fieldName'], $this->commandData->excludedFields)) {
                continue;
            }
            switch (strtolower($field['fieldType'])) {
                case 'integer':
                case 'smallinteger':
                case 'long':
                case 'bigint':
                    $rule = '"' . $field['fieldName'] . '" => "integer"';
                    break;
                case 'double':
                    $rule = '"' . $field['fieldName'] . '" => "double"';
                    break;
                case 'float':
                case 'decimal':
                    $rule = '"' . $field['fieldName'] . '" => "float"';
                    break;
                case 'boolean':
                    $rule = '"' . $field['fieldName'] . '" => "boolean"';
                    break;
                case 'string':
                case 'char':
                case 'text':
                case 'enum':
                    $rule = '"' . $field['fieldName'] . '" => "string"';
                    break;
                case 'password':
                    $rule = '"' . $field['fieldName'] . '" => "string"';
                    break;
                case 'date':
                    $rule = '"' . $field['fieldName'] . '" => "string"';
                    break;
                case 'dateTime':
                    $rule = '"' . $field['fieldName'] . '" => "string"';
                    break;
                default:
                    $rule = '';
                    break;
            }

            if (!empty($rule)) {
                $casts[] = $rule;
            }
        }

        return $casts;
    }

    public function generateSwagger($fields, $fillables)
    {
        $template = $this->commandData->templatesHelper->getTemplate("Model", 'swagger');

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $template);

        $templateData = str_replace('$REQUIRED_FIELDS$', implode(", ", $fillables), $templateData);

        $propertyTemplate = $this->commandData->templatesHelper->getTemplate("Property", 'swagger');

        $properties = SwaggerTemplateUtil::preparePropertyFields($propertyTemplate, $fields);

        $templateData = str_replace('$PROPERTIES$', implode(",\n", $properties), $templateData);

        return $templateData;
    }
}
