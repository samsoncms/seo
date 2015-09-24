<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 24.09.2015
 * Time: 18:54
 */

namespace samsoncms\seo\sitemap;

use samson\activerecord\dbQuery;

/**
 * Class Xml for parse and generate xml site map
 * @package samsoncms\seo\sitemap
 */
class Xml {

    public $query;

    /** @var  String Current host */
    public $currentUrl;

    public function __construct()
    {
        // Save current host
        $this->currentHost = "http://$_SERVER[HTTP_HOST]";

        $this->query = new dbQuery('material');
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
                    //->cond('Published', 1)
                    // Remove later
                    ->limit(5000)
                    ->fields('Url', $urls);

                return $urls;
            }
        } else {
            throw new \Exception('Structure don\'t exists');
        }
    }

    /**
     * Get content with url of pages
     * @param $urls
     * @param $category
     * @return string
     */
    public function getXmlContentByUrls($urls, $category)
    {
        // Get full path
        $path = $this->currentHost . $category. '/';

        // Add separator
        $tagAsSeparator = "</loc></url><url><loc>{$path}";

        // Implode all element of array and concatenate separator with right xml tags and concatenate first and last tags
        $xml = '<url><loc>'.$path.implode($tagAsSeparator, $urls).'</loc></url>';

        return $xml;
    }

    /**
     * Get xml text with site map by category(path)
     * @param $structure
     * @param $path
     * @return string
     * @throws \Exception
     */
    public function getSiteMapForCategory($structure, $path)
    {
        // Get urls
        $urls = $this->getUrlByStructure($structure);

        // Get content
        $innerBlock = $this->getXmlContentByUrls($urls, $path);

        // Concatenate header and footer of xml text
        $result = "<?xml version='1.0' standalone='yes'?>".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'.$innerBlock.'</urlset>';

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

}