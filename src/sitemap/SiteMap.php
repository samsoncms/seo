<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 24.09.2015
 * Time: 18:31
 */

namespace samsoncms\seo\sitemap;

use samson\activerecord\dbQuery;
use samsoncms\seo\schema\Main;

/**
 * Class SiteMap
 * @package samsoncms\seo\sitemap
 */
class SiteMap {

    public $query;

    /** @var int Id of material table */
    // TODO get this id dynamically
    public $siteMapId = 182;

    public function __construct()
    {
        $this->query = new dbQuery('material');
    }

    /**
     * Get params from seo module
     * @return array
     * @throws \Exception
     */
    public function getParams(){

        $structure = null;
        // Get structure
        if ($this->query->className('structure')->cond('Url', Main::URL_STRUCTURE)->first($structure)) {

            // Get main material which stores all data
            $mainMaterial = null;
            if ($this->query->className('\samson\cms\CMSMaterial')->cond('MaterialID', $structure->MaterialID)->first($mainMaterial)) {

                // Get table by sitemap structure
                $table = $mainMaterial->getTable($this->siteMapId);
                $result = array();

                // Get data in right form
                foreach ($table as $row) {

                    $param = array();

                    // Iterate all fields of SiteMap schema and get right name of fields
                    // TODO Fixed it!!! not use instance for getting particular field of if
                    $fields = (new \samsoncms\seo\schema\control\SiteMap())->fields;
                    for ($countOfField = 0; $countOfField < count($fields); $countOfField++) {

                        $param[$fields[$countOfField]['Name']] = $row[$countOfField];
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
}