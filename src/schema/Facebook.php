<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 12:45
 */

namespace samson\cms\seo\schema;

class Facebook extends Schema{

    /** @var string Id of schema */
    public $id = 'facebook';

    /** @var array Unique fields of schema */
    public $fields = array();

    /** @var string Name of structure */
    public $structureName = 'SEO_name_structure_facebook';

    /** @var string Url of structure */
    public $structureUrl = 'SEO_name_structure_facebook';

    public $view = 'www/template/facebook';

    /** @var array Relation between name of fields and particular meta tags */
    public $relations = array(
        '__SEO_Title' => 'og:title',
        '__SEO_Description' => 'og:description',
        '__SEO_Image' => 'og:image',
        '__SEO_Url' => 'og:url',
        '__SEO_SiteName' => 'og:site_name',
    );
}