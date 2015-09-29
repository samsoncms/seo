<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 17:40
 */

namespace samsoncms\seo;

use samsoncms\seo\schema\material\Publisher;
use samsoncms\seo\schema\Schema;

class Display
{

    /** @var \samson\activerecord\dbQuery */
    public $query;

    /**
     * Init query
     * @param $query
     */
    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Get data from passed material
     * @param $fieldName
     * @param $material
     * @return null
     * @throws \Exception
     */
    public function getDataField($fieldName, $material)
    {
        // Check and get data
        if (!empty($material)) {
            if (isset($material[$fieldName])) {
                if (!empty($material[$fieldName])) {
                    return $material[$fieldName];
                } else {
                    return null;
                }
            }

            return null;
        }

        return null;
    }

    /**
     * Get data from hierarchy of inheritance
     * @param $schema
     * @param $fieldName
     * @param $material
     * @param bool $deep search in parent structures then change current material to parent
     * @return null
     * @throws \Exception
     */
    public function getFieldBySchema($schema, $fieldName, $material, $deep = false)
    {

        // Set field name with prefix of schema
        $fieldNameFull = $fieldName . '_' . $schema->id;
        $fieldValue = $this->getDataField($fieldNameFull, $material);

        // If in the current schema need value wasn't found then find in sibling schemas
        if (empty($fieldValue)) {

            // Find value in all reserved schemas
            foreach (Schema::getMaterialSchema() as $schemaFind) {

                // If it is not deep search then avoid equal schema
                if ($schema->id == $schemaFind->id && $deep == false) {
                    continue;
                }

                // Set field name with prefix of schema
                $fieldNameFull = $fieldName . '_' . $schemaFind->id;

                // If it is deep search i.e search in parent structures then change current material to parent
                if ($deep == true) {
                    $material = $this->getNestedMaterial(Schema::getMainSchema()->getStructure());
                }

                // Get data from material
                $fieldValue = $this->getDataField($fieldNameFull, $material);

                // End if value was found
                if (!empty($fieldValue)) {
                    return $fieldValue;
                }
            }

            // Valued was not found
            return null;
        }

        return $fieldValue;
    }

    /**
     * Find data
     * @param $schema
     * @param $fieldName
     * @param $material
     * @return null
     */
    public function findField($schema, $fieldName, $material)
    {

        // Find data in schema or sibling schemas
        $value = $this->getFieldBySchema($schema, $fieldName, $material);

        // Find in parent structures
        if (empty($value)) {

            // Get parent material
            $material = $this->getNestedMaterial($schema->getStructure());

            // Get value
            $value = $this->getFieldBySchema($schema, $fieldName, $material, true);
        }

        return $value;
    }

    /**
     * Get material by url
     * @param $url
     * @return mixed
     */
    public function getMaterialByUrl($url)
    {

        return $this->query->className('samson\cms\CMSMaterial')->Url($url)->first();
    }

    /**
     * Get expected url of material
     * @return string last item of uri
     */
    public function getItemUrl()
    {
        // Get url
        $url = url()->text();

        // Remove last slash
        $url = preg_replace('/\/$/', '', $url);

        // Get last part of url
        if (strpos($url, '/') !== false) {
            $arr = explode('/', $url);
            $path = array_pop($arr);
        } else {
            $path = $url;
        }

        // Get path
        return $path;
    }

    /**
     * Get nested material in structure
     * @return null
     */
    public function getNestedMaterial($structure)
    {

        // Get nested material
        $material = null;
        $material = dbQuery('\samson\cms\CMSMaterial')->cond('MaterialID', $structure->MaterialID)->first();

        return $material;
    }

    /**
     * Get all views which not assign to any material
     * @param $renderer
     * @return String
     */
    public function getCommonViews($renderer)
    {
        $html = '';
        // Get all single schemas
        foreach (array(new Publisher()) as $schema) {

            // Get main material
            $material = $this->getNestedMaterial(Schema::getMainSchema()->getStructure());

            // Get relation in schema
            foreach ($schema->relations as $fieldName => $alias) {

                // Get value
                $content = $material[$fieldName . '_' . $schema->id];

                // Render
                $html .= $renderer->view($schema->view)->name($alias)->content($content)->output() . "\n";
            }
        }

        return $html;
    }
}