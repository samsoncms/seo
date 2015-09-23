<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 12:45
 */

namespace samsoncms\seo\schema\material;

use samsoncms\seo\schema\Schema;

class Meta extends Schema implements MaterialSchema{

    /** @var string Id of schema */
    public $id = 'meta';

    /** @var array Unique fields of schema */
    public $fields = array(
        array(
            'Name' => '__SEO_Keywords',
            'Description' => 'Ключевые слова',
            'Type' => '0',
        ),
    );

    /** @var string Name of structure */
    public $structureName = 'SEO_name_structure_meta';

    /** @var string Url of structure */
    public $structureUrl = 'SEO_name_structure_meta';

    /** @var array Relation between name of fields and particular meta tags */
    public $relations = array(
        '__SEO_Title' => 'title',
        '__SEO_Description' => 'description',
        '__SEO_Keywords' => 'keywords',
    );
}