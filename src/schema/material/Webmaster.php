<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 22.09.2015
 * Time: 17:06
 */

namespace samsoncms\seo\schema\material;

use samsoncms\seo\schema\material\MaterialSchema;
use samsoncms\seo\schema\Schema;
use samsoncms\seo\render\Element;
use samsoncms\seo\render\Button;
use samsonphp\event\Event;
use samsoncms\seo\schema\control\ControlSchema;

class Webmaster extends \samsoncms\seo\schema\Schema implements MaterialSchema
{
    /** @var string Id of schema */
    public $id = 'webmaster';

    public $view = 'www/template/webmaster';

    public $visibility = true;

    /** @var string Id seo tabs */
    public $tabs = 'seo_field_tab';

    public $assignMainFields = false;

    /** @var array Unique fields of schema */
    public $fields = array(
        array(
            'Name' => '__SEO_Dynamic_Yandex_Webmaster_Tag',
            'Description' => 'Yandex Webmaster',
            'Type' => '0',
        ),
        array(
            'Name' => '__SEO_Dynamic_Google_Webmaster_Tag',
            'Description' => 'Google Webmaster',
            'Type' => '0',
        ),
    );

    /** @var string Name of structure */
    public $structureName = 'SEO_name_structure_seo_webmaster';

    /** @var string Url of structure */
    public $structureUrl = 'SEO_name_structure_seo_webmaster';

    /** @var array Relation between name of fields and particular meta tags */
    public $relations = array(
        '__SEO_Dynamic_Yandex_Webmaster_Tag' => 'yandex-verification',
        '__SEO_Dynamic_Google_Webmaster_Tag' => 'google-site-verification'
    );
}
