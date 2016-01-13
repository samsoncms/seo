<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 17:40
 */

namespace samsoncms\seo;

use samsoncms\api\SEONameStructureMetaQuery;
use samsoncms\api\TestQuery;
use samsoncms\seo\schema\control\seo\Dynamic;
use samsoncms\seo\schema\material\Facebook;
use samsoncms\seo\schema\material\Publisher;
use samsoncms\seo\schema\Schema;

class Display
{

    /** @var \samson\activerecord\dbQuery */
    public $query;

    /**
     * @var array
     */
    public $materialByUrlCache = array();

    /**
     * Init query
     * @param $query
     * @param $mainStructure
     */
    public function __construct($query, $mainStructure)
    {
        $this->query = $query;

        // Get material of main structure
        $this->mainMaterial = dbQuery('\samson\cms\CMSMaterial')
            ->cond('MaterialID', $mainStructure->MaterialID)
            ->first();
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
                    $material = $this->getMainMaterial();
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
            $material = $this->getMainMaterial();

            // Get value
            $value = $this->getFieldBySchema($schema, $fieldName, $material, true);
            
            // Append server host to image link
            if ($fieldName == '__SEO_Image' && $value != '') {
                $value = 'http://'.$_SERVER['HTTP_HOST'].$value;
            }

            // If site name in the facebook not found then set it as absolute url this page
            if (($value == '') && ($schema instanceof Facebook)) {
                if ($fieldName == '__SEO_Url') {
                    $value = 'http://'.$_SERVER['HTTP_HOST'].'/'.url()->text();
                }
            }
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
        if (empty($url)) {
            return null;
        }

        // If this material located in cache then return it
        if (isset($this->materialByUrlCache[$url])) {
            return $this->materialByUrlCache[$url];
        }

        $result = $this->query->className('samson\cms\CMSMaterial')->cond('Url', $url)->first();

        $this->materialByUrlCache[$url] = $result;

        return $result;
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
     * @param $structure
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
     * Get material of main structure
     * @return mixed
     */
    public function getMainMaterial()
    {
        return $this->mainMaterial;
    }

    /**
     * Get all views which not assign to any material
     * @param $renderer
     * @return String
     */
    public function getCommonViews($renderer)
    {
        $html = '';

        // Get main material
        $material = $this->getMainMaterial();

        // Get all single schemas
        foreach (array(new Publisher()) as $schema) {

            // Get relation in schema
            foreach ($schema->relations as $fieldName => $alias) {

                // Get value
                $content = trim($material[$fieldName . '_' . $schema->id]);

                if (isset($content{0})) {
                    // Render
                    $html .= $renderer->view($schema->view)->name($alias)->content($content)->output() . "\n";
                }
            }
        }

        return $html;
    }
}
