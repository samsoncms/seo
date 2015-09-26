<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 12:45
 */

namespace samsoncms\seo\schema\structure;

use samsoncms\seo\schema\Schema;

class Publisher extends Schema implements StructureSchema
{

    /** @var string Id of schema */
    public $id = 'publisher';

    public $view = 'www/template/publisher';

    /** @var array Unique fields of schema */
    public $fields = array(
        array(
            'Name' => '__SEO_Publisher',
            'Description' => 'Издатель',
            'Type' => '0',
        ),
    );

    /** @var string Name of structure */
    public $structureName = 'SEO_name_structure_publisher';

    /** @var string Url of structure */
    public $structureUrl = 'SEO_name_structure_publisher';

    /** @var array Relation between name of fields and particular meta tags */
    public $relations = array(
        '__SEO_Publisher' => 'href',
    );
}
