<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 22.09.2015
 * Time: 17:06
 */

namespace samsoncms\seo\schema\control\seo;

use samsoncms\seo\schema\Schema;
use samsoncms\seo\render\Element;
use samsoncms\seo\render\Button;
use samsonphp\event\Event;
use samsoncms\seo\schema\control\ControlSchema;

class Dynamic extends \samsoncms\seo\schema\Schema implements ControlSchema
{

    /** @var string Id of schema */
    public $id = 'dynamic';

    public $visibility = true;

    /** @var string Id seo tabs */
    public $tabs = 'seo_field_tab';

    public $elements = array(
        array(
            'Type' => 'Info',
            'Group' => 'Info',
            // If this element have to be nested in table
            'Nested' => true,
            // If we need to hide element set true
            'Hide' => false,
            'Data' => array(
                'View' => 'control/seo/info'
            )
        ),
    );

    /** @var array Unique fields of schema */
    public $fields = array(
        array(
            'Name' => '__SEO_Dynamic_Seo_Tag',
            'Description' => 'Теги',
            'Type' => '0',
        ),
    );

    /** @var string Name of structure */
    public $structureName = 'SEO_name_structure_seo_dynamic';

    /** @var string Url of structure */
    public $structureUrl = 'SEO_name_structure_seo_dynamic';

    /** @var array Relation between name of fields and particular meta tags */
    public $relations = array();
}
