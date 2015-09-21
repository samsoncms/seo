<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 12:45
 */

namespace samson\cms\seo\schema;

class Google extends Schema{

    /** @var string Id of schema */
    public $id = 'google';

    /** @var array Unique fields of schema */
    public $fields = array();

    /** @var string Name of structure */
    public $structureName = 'SEO_name_structure_google';

    /** @var string Url of structure */
    public $structureUrl = 'SEO_name_structure_google';

    public $view = 'www/template/google';

    /** @var array Relation between name of fields and particular meta tags */
    public $relations = array(
        '__SEO_Title' => 'name',
        '__SEO_Description' => 'description',
        '__SEO_Image' => 'image',
    );
}