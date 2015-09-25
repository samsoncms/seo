<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 22.09.2015
 * Time: 17:06
 */

namespace samsoncms\seo\schema\control\sitemap;

use samsoncms\seo\schema\control\ControlSchema;
use samsoncms\seo\schema\Schema;
use samsoncms\seo\render\Element;
use samsoncms\seo\render\Button;
use samsonphp\event\Event;

class Statical extends \samsoncms\seo\schema\Schema implements ControlSchema
{

    /** @var string Id of schema */
    public $id = 'static';

    public $view = 'www/template/publisher';

    public $elements = array(
        array(
            'Type' => 'Button',
            'Group' => 'Refresh',
            // If this element have to be nested in table
            'Nested' => true,
            // If we need to hide element set true
            'Hide' => true,
            'Data' => array(
                'Title' => 'Update',
                'Link' => 'refresh'
            )
        ),
        array(
            'Type' => 'AnswerBlock',
            'Group' => 'Refresh',
            'Hide' => true,
            'Data' => array()
        ),
    );

    /** @var array Unique fields of schema */
    public $fields = array(
        array(
            'Name' => '__SEO_Link',
            'Description' => 'Link',
            'Type' => '0',
        ),
    );

    /** @var string Name of structure */
    public $structureName = 'SEO_name_structure_sitemap_static';

    /** @var string Url of structure */
    public $structureUrl = 'SEO_name_structure_sitemap_static';

    /** @var array Relation between name of fields and particular meta tags */
    public $relations = array(
        '__SEO_Structure' => '',
        '__SEO_Link' => '',
    );
}