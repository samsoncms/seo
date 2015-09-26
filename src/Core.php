<?php

namespace samsoncms\seo;

use samson\activerecord\dbRecord;
use samson\activerecord\structure;
use samson\cms\CMSMaterial;
use samsoncms\seo\Migrate;
use samsoncms\seo\schema\material\Facebook;
use samsoncms\seo\schema\Main;
use samsoncms\seo\schema\Schema;
use samson\core\CompressableService;
use samsoncms\seo\sitemap\SiteMap;
use samsoncms\seo\sitemap\Xml;
use samsonphp\event\Event;
use WebDriver\Exception;

/**
 * Show mata links for social networks
 * For use this module need create structure and write its id to structureId in config this module
 * Then create depend! material and field for store data in this structure
 * And use this <?php m('seo_tags')->render('')?> for insert tags into head tag
 * @author Molodyko Ruslan <molodyko@samsonos.com>
 * @copyright 2015 SamsonOS
 * @version 1.1
 */
class Core extends CompressableService
{
    /** Module identifier */
    public $id = 'seo_tags';

    /** @var string main view */
    public $viewIndex = 'www/index';

    /** @var string view for render title of page */
    public $viewTitle = 'www/title';

    /** @var \samson\activerecord\dbQuery */
    protected $query;

    public function init(array $params = array())
    {

        // Save dbQuery instance
        $this->query = dbQuery('structure');

        // Fire new event after creating form tabs
        Event::subscribe('samsoncms.material.form.created', array($this, 'renderMaterialTab'));
    }

    /**
     * Create structures
     * @return bool
     */
    public function prepare()
    {

        $this->query = dbQuery('structure');

        $migrate = new \samsoncms\seo\Migrate($this->query);

        // Execute migrations
        $migrate->migrate();

        return parent::prepare();
    }


    /**
     * Handler created form event
     * @param \samsoncms\app\material\form\Form $form
     * @param $renderer
     * @param $query
     * @param $entity
     */
    public function renderMaterialTab(\samsoncms\app\material\form\Form &$form, $renderer, $query, $entity)
    {

        $migrate = new \samsoncms\seo\Migrate($this->query);

        // Execute migrations
        $migrate->migrate();
        $tab = new \samsoncms\seo\tab\Tab($renderer, $query, $entity);
        $form->tabs[] = $tab;

        $tab = new \samsoncms\seo\tab\ControlTab($renderer, $query, $entity);
        $form->tabs[] = $tab;

    }

    /**
     * Refresh site map structure on the site
     */
    public function __async_strefresh()
    {

        // Call instance
        $st = new SiteMap();

        try {

            // Do refresh
            $response = $st->refresh();

            // If error was happen then answer error message
        } catch (\Exception $e) {

            return array('status' => false, 'error' => $e->getMessage());
        }

        // Get result
        return array('status' => true, 'time' => $response['time'], 'count' => $response['count']);
    }

    /**
     * Render tags
     */
    public function __handler()
    {

        // Class for work with data
        $display = new Display($this->query);

        // Iterate all reserved schemas and output their data
        $html = '';
        foreach (Schema::getMaterialSchema() as $schema) {

            // Get current material
            $material = $display->getMaterialByUrl($display->getItemUrl());

            // Out comment
            $html .= "<!-- {$schema->id} -->";

            // Iterate all relations
            foreach ($schema->relations as $fieldName => $alias) {

                // Find data in hierarchy of structures
                $content = $display->findField($schema, $fieldName, $material);

                // EXCLUDE Render title of page
                if ($schema->id == 'meta' && $fieldName == '__SEO_Title') {
                    $html .= $this->view($this->viewTitle)->title($content)->output();
                }

                // If content not empty render field(meta tag)
                if (!empty($content)) {

                    // Save html view
                    $html .= $this->view($schema->view)->name($alias)->content($content)->output() . "\n";
                }
            }
        }

        // Get all view of not assigned(single) material
        $html .= $display->getCommonViews($this);

        // Get meta i18n links
        m('i18n')->action('meta');
        $i18n = m('i18n')->output();

        // Show result
        $this->view($this->viewIndex)->i18n($i18n)->content($html);
    }
}
