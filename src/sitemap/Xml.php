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
class Xml
{

    /** @var  String Current host */
    public $currentHost;

    /** @var  String Base root */
    public $baseRoot;


    public function __construct()
    {
        // Get www directory
        $this->baseRoot = $_SERVER['DOCUMENT_ROOT'];

        // Save current host
        $this->currentHost = "http://$_SERVER[HTTP_HOST]";
    }

    /**
     * Get content with url of pages
     * @param $urls
     * @param $category
     * @return string
     */
    public function getXmlContentByUrls($urls, $category)
    {
//        $urls = array_merge($urls, $urls);
//        $urls = array_merge($urls, $urls);
//        $urls = array_merge($urls, $urls);
//        $urls = array_merge($urls, $urls);
//        $urls = array_merge($urls, $urls);
//        $urls = array_merge($urls, $urls);
//        $urls = array_merge($urls, $urls);

        //trace(count($urls), 1);

        // Get full path
        $path = $this->currentHost . $category . DIRECTORY_SEPARATOR;

        // Add separator
        $tagAsSeparator = "</loc></url><url><loc>{$path}";

        // Implode all array elements and concatenate separator with right xml tags and concatenate first and last tags
        $xml = '<url><loc>' . $path . implode($tagAsSeparator, $urls) . '</loc></url>';

        return $xml;
    }

    /**
     * @param $url
     * @return string
     */
    public function getSingleUrl($url)
    {

        return "<loc><url>$url</url></loc>";
    }

    /**
     * Get xml text with site map by category(path)
     * @param $urls
     * @param $path
     * @return string
     * @throws \Exception
     */
    public function generateSiteMapForCategory($urls, $path)
    {

        // Get content
        $innerBlock = $this->getXmlContentByUrls($urls, $path);

        // Concatenate header and footer of xml text
        $result = "<?xml version='1.0' standalone='yes'?>" .
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . $innerBlock . '</urlset>';

        return $result;
    }

    public function generateIndexSiteMap($params, $filePrefix = '')
    {
        $xmlIndex = "<?xml version='1.0' standalone='yes'?>".
                '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"></urlset>';

        $index = new \SimpleXMLElement($xmlIndex);

        // Iterate all files and create sitemap file content
        foreach ($params as $param) {

            // Get file name
            $fileName = $param['__SEO_Link'];

            // Remove prefix slash
            $fileName = preg_replace('/^\//', '', $fileName);

            // Exchange all slash to dash
            $fileName = preg_replace('/\//', '-', $fileName).'.xml';

            // Add new item to main sitemap file
            $item = $index->addChild('sitemap');
            $item->addChild('loc', $this->currentHost.DIRECTORY_SEPARATOR.$filePrefix.$fileName);
            $item->addChild('lastmod', date('c', time()));
        }

        // Get results
        $result = $index->saveXML();

        return $result;
    }

    public function saveXmlToFile($filename, $content)
    {

        // Get full directory to site map file
        $path = $this->baseRoot.DIRECTORY_SEPARATOR.$filename;

        // Delete file if exists
        if (file_exists($path)) {
            unlink($path);
        }

        // Write XML to file
        $file = fopen($path, "w");
        fwrite($file, $content);
        fclose($file);
    }
}
