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

class SocialLink extends \samsoncms\seo\schema\Schema implements ControlSchema
{

    /** @var string Id of schema */
    public $id = 'social_link';

    public $view = 'template/dynamic';

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
                'View' => 'control/seo/infoSocialLink'
            )
        ),
    );

    /** @var array Unique fields of schema */
    public $fields = array(
        array(
            'Name' => '__SEO_Social_link_name',
            'Description' => 'Имя',
            'Type' => '0',
        ),
        array(
            'Name' => '__SEO_Social_link_content',
            'Description' => 'Ссылка',
            'Type' => '0',
        ),
    );

    /** @var string Name of structure */
    public $structureName = 'SEO_name_structure_social_link';

    /** @var string Url of structure */
    public $structureUrl = 'SEO_name_structure_social_link';

    /** @var array Relation between name of fields and particular meta tags */
    public $relations = array();
}
