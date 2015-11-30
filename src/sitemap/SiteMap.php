<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 24.09.2015
 * Time: 18:31
 */

namespace samsoncms\seo\sitemap;

use samson\activerecord\dbQuery;
use samsoncms\seo\schema\control\ControlSchema;
use samsoncms\seo\schema\control\sitemap\Dynamic;
use samsoncms\seo\schema\control\sitemap\Statical;
use samsoncms\seo\schema\Main;
use samsonframework\orm\Relation;

/**
 * Class SiteMap
 * @package samsoncms\seo\sitemap
 */
class SiteMap
{

    public $query;

    public $filePrefix = 'sitemap-';

    public $mainMapName = 'sitemap';

    public function __construct()
    {
        $this->query = new dbQuery('material');

        $this->schemas = array(
            new Dynamic(),
            new Statical()
        );
    }

    public function getSchemas()
    {

        return $this->schemas;
    }

    public function refresh()
    {

        //elapsed('start');
        $start = microtime(true);

        $xml = new Xml();

        // Count schemas
        $count = 0;
        $isStaticExists = false;
        $mainParams = array();
        foreach ($this->getSchemas() as $schema) {

            // Get params of site map
            $params = $this->getParams($schema);

            if ($schema instanceof Dynamic) {

                // Execute some structure
                list($countLocal, $linksLocal) = $this->executeDynamicParams(
                    $params,
                    $xml,
                    false
                );

                $count += $countLocal;

                $mainParams = array_merge($mainParams, $linksLocal);

            } elseif ($schema instanceof Statical) {

                // Execute some structure
                $countStaticLinks = $this->executeStaticalParams($params, $xml);
                $count += $countStaticLinks;

                // Set true if there some links
                if ($countStaticLinks > 0) {
                    $isStaticExists = true;
                }
            }
        }

        // If static elements exists then add static file to the main site map file
        if ($isStaticExists) {

            // Add static file to the main site map
            $mainParams[] = array('__SEO_Link' => '/static');
        }

        // If there not nothing to generate
        if ($count > 0) {

            // Get content of main site map file
            $mainSiteMapContent = $xml->generateIndexSiteMap($mainParams, $this->filePrefix);

            // Save
            $xml->saveXmlToFile($this->mainMapName . '.xml', $mainSiteMapContent);
        }

        $time_elapsed_secs = microtime(true) - $start;

        //elapsed('end');

        return array('time' => $time_elapsed_secs, 'count' => $count);
    }

    /**
     * @param $params
     * @param $xml
     * @return int
     * @throws \Exception
     */
    public function executeStaticalParams($params, $xml)
    {
        $count = 0;

        // Iterate all param and create site map files
        $links = array();
        foreach ($params as $param) {

            // Get category pattern "/path"
            $links[] = $param['__SEO_Link'];

            $count++;
        }

        // There not any static links
        if ($count == 0) {
            return $count;
        }
        // Get result xml text for category
        $result = $xml->generateSiteMapForCategory($links, '');

        $fileName = 'static.xml';

        // Create site map file for current category
        $xml->saveXmlToFile($this->filePrefix . $fileName, $result);

        return $count;
    }

    /**
     * @param $params
     * @param $xml
     * @return int
     * @throws \Exception
     */
    public function executeDynamicParams($params, $xml, $recursive = false)
    {
        $count = 0;
        $links = array();

        // Iterate all param and create site map files
        foreach ($params as $param) {

            // Get category pattern "/path"
            $link = $param['__SEO_Link'];

            // Get url of materials
            $urls = $this->getUrlByStructure($param['__SEO_Structure']);

            $count += count($urls);

            // If there is some items
            if (count($urls) > 0) {

                // Get result xml text for category
                $result = $xml->generateSiteMapForCategory($urls, $link);

                // Remove prefix slash
                $fileName = preg_replace('/^\//', '', $link);

                // Exchange all slash to dash
                $fileName = preg_replace('/\//', '-', $fileName) . '.xml';

                // Create site map file for current category
                $xml->saveXmlToFile($this->filePrefix . $fileName, $result);

                $links[] = $param;
            }

            // If need recursively execute structures
            if ((isset($param['__SEO_IsRecursive']))&&($param['__SEO_IsRecursive'] == true)||$recursive) {

                $structures = null;
                $this->query->className('structure')
                    ->cond('ParentID', $param['__SEO_Structure'])
                    ->exec($structures);

                foreach ($structures as $structure) {
                    $param = array('__SEO_Link'=>$link.'/'.$structure->Url, '__SEO_Structure' => $structure->id);
                    list($countLocal, $linksLocal) = $this->executeDynamicParams(array($param), $xml, true);
                    $count += $countLocal;
                    $links = array_merge($links, $linksLocal);
                }
            }
        }

        return array($count, $links);
    }

    /**
     * Get params from seo module
     * @return array
     * @throws \Exception
     */
    public function getParams(ControlSchema $controlSchema)
    {

        $structure = null;
        // Get structure
        if ($this->query->className('structure')->cond('Url', Main::URL_STRUCTURE)->first($structure)) {

            // Get main material which stores all data
            $mainMaterial = null;
            if ($this->query
                ->className('\samson\cms\CMSMaterial')
                ->cond('MaterialID', $structure->MaterialID)
                ->first($mainMaterial)
            ) {

                // Get table by sitemap structure
                $table = $mainMaterial->getTable($controlSchema->getStructure()->id);
                $result = array();

                // Get related structures
                $relatedStructures = $this->getRelatedStructuresInMaterial(
                    $controlSchema,
                    $mainMaterial
                );

                // Get data in right form
                $rowCount = 0;
                foreach ($table as $row) {

                    $param = array();

                    // Iterate all fields of SiteMap schema and get right name of fields
                    // TODO Fixed it!!! not use instance for getting particular field of if
                    $fields = $controlSchema->fields;
                    for ($countOfField = 0; $countOfField < count($fields); $countOfField++) {

                        $param[$fields[$countOfField]['Name']] = $row[$countOfField];
                    }

                    // If current schema if Dynamic the set related structures to them
                    if ($controlSchema->id == 'dynamic') {

                        $param['__SEO_Structure'] = $relatedStructures[$rowCount++];
                    }

                    // If this row not active don't save the data
                    if (isset($param['__SEO_IsActive']) && ($param['__SEO_IsActive'] == false)) {
                        continue;
                    }

                    $result[] = $param;
                }

                return $result;

            } else {

                throw new \Exception('Main material not found');
            }

        } else {
            throw new \Exception('Structure not found');
        }
    }

    public function getRelatedStructuresInMaterial($controlSchema, $mainMaterial)
    {

        // Get all material in this schema
        $materialIds = null;
        $this->query->className('structurematerial')
            ->cond('StructureID', $controlSchema->getStructure()->id)
            ->cond('Active', 1)
            ->fields('MaterialID', $materialIds);

        // Get all material which is child of main material
        $materials = null;
        $this->query->className('material')
            ->cond('parent_id', $mainMaterial->MaterialID)
            ->cond('MaterialID', $materialIds)
            ->fields('MaterialID', $materials);

        $result = array();

        // Get field of structure select
        $field = $this->query->className('field')
            ->cond('Name', '__SEO_Structure_dynamic')
            ->first();

        // Iterate materials and get them values
        foreach ($materials as $id) {

            $values = null;
            $this->query->className('materialfield')
                ->cond('FieldID', $field->FieldID)
                ->cond('MaterialID', $id)
                ->fields('Value', $values);

            $val = array();
            // Save values in array
            foreach ($values as $value) {

                if (!empty($value)) {

                    $val[] = $value;
                }
            }
            $result[] = $val;
        }

        return $result;
    }

    /**
     * Check if structure exists
     * @param $structureId
     * @return mixed
     */
    public function isStructureExists($structureId)
    {
        return $this->query->className('structure')->cond('StructureID', $structureId)->count();
    }

    /**
     * Get all url which belong to criteria
     * @param $structureId
     * @return null
     * @throws \Exception
     */
    public function getUrlByStructure($structureId)
    {
        // Check if structure exists
        if ($this->isStructureExists($structureId)) {

            // Get all material which belong to criteria
            $materialIds = null;
            if ($this->query
                ->className('structurematerial')
                ->cond('StructureID', $structureId)
                ->fields('MaterialID', $materialIds)
            ) {

                // Get urls
                $urls = null;
                $this->query->className('material')
                    ->cond('MaterialID', $materialIds)
                    ->cond('Published', 1)
                    // Remove later
                    //->limit(5000)
                    ->fields('Url', $urls);

                return $urls;
            }
        } else {
            throw new \Exception('Structure don\'t exists');
        }
    }
}
