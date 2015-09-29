<?php

namespace Quantimodo\Generator\Utils;

class SwaggerTemplateUtil
{
    public static function preparePropertyFields($template, $fields)
    {
        $templates = [];

        foreach ($fields as $field => $type) {
            $type = explode(":", $type);
            if (count($type) > 1) {
                $format = $type[1];
            } else {
                $format = "";
            }
            $type = $type[0];
            $propertyTemplate = str_replace('$FIELD_NAME$', $field, $template);
            $propertyTemplate = str_replace('$DESCRIPTION$', $field, $propertyTemplate);
            $propertyTemplate = str_replace('$FIELD_TYPE$', $type, $propertyTemplate);
            if (!empty($format)) {
                $format = ",\n *          format=\"" . $format . "\"";
            }
            $propertyTemplate = str_replace('$FIELD_FORMAT$', $format, $propertyTemplate);
            $templates[] = $propertyTemplate;
        }

        return $templates;
    }
}
