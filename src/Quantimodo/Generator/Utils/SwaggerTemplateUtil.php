<?php

namespace Quantimodo\Generator\Utils;

class SwaggerTemplateUtil
{
    public static function preparePropertyFields($template, $fields)
    {
        $templates = [];

        foreach ($fields as $field) {
            $fieldName = $field['name'];
            $type = explode(":", $field['type']);
            if (count($type) > 1) {
                $format = $type[1];
            } else {
                $format = "";
            }
            $type = $type[0];
            $propertyTemplate = str_replace('$FIELD_NAME$', $fieldName, $template);
            $description = $field['description'];
            if (empty($description)) {
                $description = $fieldName;
            }
            $propertyTemplate = str_replace('$DESCRIPTION$', $description, $propertyTemplate);
            $propertyTemplate = str_replace('$FIELD_TYPE$', $type, $propertyTemplate);
            if (!empty($format)) {
                $format = ",\n *          format=\"" . $format . "\"";
            }
            $propertyTemplate = str_replace('$FIELD_FORMAT$', $format, $propertyTemplate);
            $templates[] = $propertyTemplate;
        }

        return $templates;
    }

    public static function prepareIndexParameters($template, $fields)
    {
        $templates = [];

        foreach ($fields as $fieldObj) {
            $field = $fieldObj['name'];
            $type = $fieldObj['type'];
            $type = explode(":", $type);
            $type = $type[0];
            $propertyTemplate = str_replace('$FIELD_NAME$', $field, $template);
            $propertyTemplate = str_replace('$DESCRIPTION$', $field, $propertyTemplate);
            $propertyTemplate = str_replace('$FIELD_TYPE$', $type, $propertyTemplate);
            $templates[] = $propertyTemplate;
        }

        return $templates;
    }
}
