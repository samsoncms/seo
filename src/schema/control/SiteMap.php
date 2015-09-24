<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 22.09.2015
 * Time: 17:06
 */

namespace samsoncms\seo\schema\control;

use samsoncms\seo\schema\Schema;
use samsoncms\seo\render\Element;
use samsoncms\seo\render\Button;
use samsonphp\event\Event;

class SiteMap extends \samsoncms\seo\schema\Schema implements ControlSchema
{

    /** @var string Id of schema */
    public $id = 'sitemap';

    public $view = 'www/template/publisher';

    public $elements = array(
        array(
            'Type' => 'Button',
            'Group' => 'Refresh',
            'Data' => array(
                'Title' => 'Refresh',
                'Link' => 'refresh'
            )
        ),
        array(
            'Type' => 'AnswerBlock',
            'Group' => 'Refresh',
            'Data' => array()
        ),
    );

    /** @var array Unique fields of schema */
    public $fields = array(
        array(
            'Name' => '__SEO_Structure',
            'Description' => 'Structure',
            'Type' => '0',
        ),
        array(
            'Name' => '__SEO_Link',
            'Description' => 'Link',
            'Type' => '0',
        ),
    );

    /** @var string Name of structure */
    public $structureName = 'SEO_name_structure_sitemap';

    /** @var string Url of structure */
    public $structureUrl = 'SEO_name_structure_sitemap';

    /** @var array Relation between name of fields and particular meta tags */
    public $relations = array(
        '__SEO_Structure' => '',
        '__SEO_Link' => '',
    );
}