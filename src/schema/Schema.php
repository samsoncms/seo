<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 16:41
 */

namespace samsoncms\seo\schema;

use samsoncms\seo\schema\control\ControlSchema;
use samsoncms\seo\schema\control\sitemap\Dynamic;
use samsoncms\seo\schema\control\sitemap\Statical;
use samsoncms\seo\schema\material\MaterialSchema;
use samsoncms\seo\schema\material\Facebook;
use samsoncms\seo\schema\material\Google;
use samsoncms\seo\schema\material\Meta;
use samsoncms\seo\schema\material\Twitter;
use samsoncms\seo\schema\material\Publisher;

/**
 * Class Schema
 * @package samson\cms\seo\schema
 */
abstract class Schema
{

    /** @var bool If this schema can be showed on all materials */
    public $visibility = false;

    /** @var string Name of structure */
    public $structureName;

    /** @var string Url of structure */
    public $structureUrl;

    /** @var string Path to view of meta tags */
    public $view = 'www/template/default';

    public $tabs = array();

    /**
     * If need assign main fields to the structure
     * @var bool
     */
    public $assignMainFields = true;

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
     * Get all schemas in seo module
     * @param String id of tab
     * @return array
     */
    public static function getAllSchemas($tab = null)
    {
        $allSchemas = array(
            new Meta(),
            new Facebook(),
            new Twitter(),
            new Google(),
            new Publisher(),
            new \samsoncms\seo\schema\control\sitemap\Dynamic(),
            new \samsoncms\seo\schema\control\seo\Dynamic(),
            new \samsoncms\seo\schema\control\seo\SocialLink(),
            new \samsoncms\seo\schema\material\Webmaster(),
            new Statical(),
        );

        // If passed tab name then return only right schemas
        if ($tab !== null) {
            $rightSchemas = array();
            foreach ($allSchemas as $schema) {
                if (is_array($schema->tabs)) {
                    if (in_array($tab, $schema->tabs)) {
                        $rightSchemas[] = $schema;
                    }
                } elseif ($tab == $schema->tabs) {
                    $rightSchemas[] = $schema;
                }
            }

            $allSchemas = $rightSchemas;
        }

        return $allSchemas;
    }

    /**
     * Get all schemas which is material type
     */
    public static function getMaterialSchema()
    {
        $schemas = array();
        // Get all
        foreach (self::getAllSchemas() as $st) {

            // If this schema is material type
            if (in_array('samsoncms\seo\schema\material\MaterialSchema', class_implements($st))) {
                $schemas[] = $st;
            }
        }

        return $schemas;
    }

    /**
     * Get all schemas which is control type
     */
    public static function getControlSchema()
    {
        $schemas = array();
        // Get all
        foreach (self::getAllSchemas() as $st) {

            // If this schema is control type
            if (in_array('samsoncms\seo\schema\control\ControlSchema', class_implements($st))) {
                $schemas[] = $st;
            }
        }

        return $schemas;
    }

    /**
     * Get main schema of seo module
     */
    public static function getMainSchema()
    {
        return new Main();
    }
}
