<?php

namespace Quantimodo\Generator;

use Config;
use Illuminate\Support\Str;
use Quantimodo\Generator\Commands\APIGeneratorCommand;
use Quantimodo\Generator\File\FileHelper;
use Quantimodo\Generator\Utils\GeneratorUtils;

class CommandData
{
    public static $COMMAND_TYPE_API = 'api';
    public static $COMMAND_TYPE_SCAFFOLD = 'scaffold';
    public static $COMMAND_TYPE_SCAFFOLD_API = 'scaffold_api';
    public $modelName;
    public $modelNamePlural;
    public $modelNameCamel;
    public $modelNamePluralCamel;
    public $modelNamespace;
    public $tableName;
    public $fromTable;
    public $skipMigration;
    public $inputFields;
    public $excludedFields = ['created_time', 'updated_time', 'created_at', 'updated_at'];
    public $swaggerTypes;
    /** @var  string */
    public $commandType;
    /** @var  APIGeneratorCommand */
    public $commandObj;
    /** @var FileHelper */
    public $fileHelper;
    /** @var TemplatesHelper */
    public $templatesHelper;
    /** @var  bool */
    public $useSoftDelete;
    /** @var  bool */
    public $paginate;
    /** @var  string */
    public $rememberToken;
    /** @var  string */
    public $fieldsFile;
    /** @var array */
    public $dynamicVars = [];

    public function __construct($commandObj, $commandType)
    {
        $this->commandObj = $commandObj;
        $this->commandType = $commandType;
        $this->fileHelper = new FileHelper();
        $this->templatesHelper = new TemplatesHelper();
    }

    public function initVariables()
    {
        $this->modelNamePlural = Str::plural($this->modelName);
        $this->modelNameCamel = Str::camel($this->modelName);
        $this->modelNamePluralCamel = Str::camel($this->modelNamePlural);
        $this->initDynamicVariables();
    }

    public function initDynamicVariables()
    {
        $this->dynamicVars = self::getConfigDynamicVariables();

        $this->dynamicVars = array_merge($this->dynamicVars, [
            '$MODEL_NAME$' => $this->modelName,
            '$MODEL_NAME_CAMEL$' => $this->modelNameCamel,
            '$MODEL_NAME_PLURAL$' => $this->modelNamePlural,
            '$MODEL_NAME_PLURAL_CAMEL$' => $this->modelNamePluralCamel,
        ]);

        if ($this->tableName) {
            $this->dynamicVars['$TABLE_NAME$'] = $this->tableName;
        } else {
            $this->dynamicVars['$TABLE_NAME$'] = $this->modelNamePluralCamel;
        }
    }

    public static function getConfigDynamicVariables()
    {
        return [

            '$BASE_CONTROLLER$' => Config::get('generator.base_controller', 'Quantimodo\Controller\AppBaseController'),
            '$NAMESPACE_CONTROLLER$' => Config::get('generator.namespace_controller', 'App\Http\Controllers'),
            '$NAMESPACE_API_CONTROLLER$' => Config::get('generator.namespace_api_controller', 'App\Http\Controllers'),
            '$NAMESPACE_REQUEST$' => Config::get('generator.namespace_request', 'App\Http\Requests'),
            '$NAMESPACE_SERVICE$' => Config::get('generator.namespace_service', 'App\Services'),
            '$NAMESPACE_MODEL$' => Config::get('generator.namespace_model', 'App\Models'),
            '$NAMESPACE_MODEL_EXTEND$' => Config::get('generator.model_extend_class',
                'Illuminate\Database\Eloquent\Model'),
            '$SOFT_DELETE_DATES$' => "\n\tprotected \$dates = ['deleted_at'];\n",
            '$SOFT_DELETE$' => "use SoftDeletes;\n",
            '$SOFT_DELETE_IMPORT$' => "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n",
            '$API_PREFIX$' => Config::get('generator.api_prefix', 'api'),
            '$API_VERSION$' => Config::get('generator.api_version', 'v1'),
            '$PRIMARY_KEY$' => 'id',
        ];
    }

    public function getInputFields()
    {
        $fields = [];

        $this->commandObj->info('Specify fields for the model (skip id & timestamp fields, will be added automatically)');
        $this->commandObj->info('Enter exit to finish');

        while (true) {
            $fieldInputStr = $this->commandObj->ask('Field: (field_name:field_database_type)', '');

            if (empty($fieldInputStr) || $fieldInputStr == false || $fieldInputStr == 'exit') {
                break;
            }

            if (!GeneratorUtils::validateFieldInput($fieldInputStr)) {
                $this->commandObj->error('Invalid Input. Try again');
                continue;
            }

            $type = $this->commandObj->ask('Enter field html input type (text): ', 'text');

            $validations = $this->commandObj->ask('Enter validations: ', false);

            $validations = ($validations == false) ? '' : $validations;

            $fields[] = GeneratorUtils::processFieldInput($fieldInputStr, $type, $validations);
        }

        return $fields;
    }

    public function addDynamicVariable($name, $val)
    {
        $this->dynamicVars[$name] = $val;
    }

    public function getDynamicVariable($name, $default = null)
    {
        if (isset($this->dynamicVars[$name])) {
            return $this->dynamicVars[$name];
        }

        return $default;
    }

    public function getSwaggerTypes()
    {
        if (!empty($this->swaggerTypes)) {
            return $this->swaggerTypes;
        }

        $fieldTypes = [];

        foreach ($this->inputFields as $field) {
            switch ($field['fieldType']) {
                case 'integer':
                case 'long':
                    $fieldTypes[$field['fieldName']] = "integer:int32";
                    break;
                case 'double':
                    $fieldTypes[$field['fieldName']] = "number:double";
                    break;
                case 'float':
                    $fieldTypes[$field['fieldName']] = "number:float";
                    break;
                case 'boolean':
                    $fieldTypes[$field['fieldName']] = "boolean";
                    break;
                case 'string':
                case 'char':
                case 'text':
                case 'enum':
                    $fieldTypes[$field['fieldName']] = "string";
                    break;
                case 'byte':
                    $fieldTypes[$field['fieldName']] = "string:byte";
                    break;
                case 'binary':
                    $fieldTypes[$field['fieldName']] = "string:binary";
                    break;
                case 'password':
                    $fieldTypes[$field['fieldName']] = "string:password";
                    break;
                case 'date':
                    $fieldTypes[$field['fieldName']] = "string:date";
                    break;
                case 'dateTime':
                    $fieldTypes[$field['fieldName']] = "string:date-time";
                    break;
                default:
                    break;
            }
        }

        $this->swaggerTypes = $fieldTypes;

        return $this->swaggerTypes;
    }
}
