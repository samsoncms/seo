<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 16:41
 */

namespace samsoncms\seo\schema;

/**
 * Class Schema
 * @package samson\cms\seo\schema
 */
abstract class Schema {

    /** @var string Name of structure */
    public $structureName;

    /** @var string Url of structure */
    public $structureUrl;

    /** @var string Path to view of meta tags */
    public $view = 'www/template/default';

    /**
     * Get structure id of current schema
     * @return null
     */
    public function getStructureId()
    {
        $structure = $this->getStructure();
        if ($structure) {
            return $structure->id;
        }
        return null;
    }

    /**
     * Get structure of current schema
     * @return null
     */
    public function getStructure()
    {
        $structure = null;
        if (dbQuery('structure')
            ->cond('Name', $this->structureName)
            ->cond('Url', $this->structureUrl)
            ->first($structure)
        ) {
            return $structure;
        }
        return null;
    }

    /**
     * Get all structures of seo module
     */
    public static function getSchemas()
    {
        return array(
            new Meta(),
            new Facebook(),
            new Twitter(),
            new Google()
        );
    }
}