<?php

namespace samsoncms\seo;

use samson\activerecord\dbRecord;
use samson\activerecord\structure;
use samson\activerecord\structurematerial;
use samson\cms\CMSMaterial;
use samsoncms\seo\Migrate;
use samsoncms\seo\render\Element;
use samsoncms\seo\schema\control\seo\Dynamic;
use samsoncms\seo\schema\material\Facebook;
use samsoncms\seo\schema\Main;
use samsoncms\seo\schema\Schema;
use samson\core\CompressableService;
use samsoncms\seo\sitemap\SiteMap;
use samsoncms\seo\sitemap\Xml;
use samsoncms\seo\tab\InfoTab;
use samsonphp\event\Event;

/**
 * Show mata links for social networks
 * For use this module need simply install it
 * @author Molodyko Ruslan <molodyko@samsonos.com>
 * @copyright 2015 SamsonOS
 * @version 1.1
 */
class Core extends CompressableService
{
    /** Module identifier */
    public $id = 'seo';

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

        // Subscribe for render index file and include seo tags to it
        Event::subscribe('core.rendered', array($this, 'templateRenderer'));

        // Subscribe for
        Event::subscribe('samson.cms.web.materialtable.add', array($this, 'addDynamicTags'));

        // Subscribe for
        Event::subscribe('samson.cms.web.materialtable.get.table', array($this, 'renderContentElement'));
    }

    /**
     * Render elements on the tabs if exists
     * @param $materialId
     * @param $structureId
     * @param $content
     */
    public function renderContentElement($materialId, $structureId, & $content)
    {
        // Iterate all control schemas and get some element
        foreach (Schema::getControlSchema() as $schema) {

            if ($schema->getStructureId() == $structureId) {

                // Render elements if exists
                $isElements = isset($schema->elements)&&(!empty($schema->elements));

                // If exists elements on the tab then show their
                if ($isElements) {

                    // Create element instance
                    $elements = new Element();

                    // Render elements of control tab
                    $contentNestedElement = $elements->renderNestedElements($schema->elements);

                    // Insert element as first child of table
                    $content = preg_replace(
                        '/\<div class=\"material_table_tab\"\>/',
                        '<div class="material_table_tab">' . $contentNestedElement,
                        $content,
                        1
                    );

                    // Get all not nested element
                    //$contentNotNestedElement = $elements->renderNotNestedElements($schema->elements);
                }

            }
        }
    }

    /**
     * Add dynamic tags into specific material
     * @param $materialId
     * @param $structureId
     */
    public function addDynamicTags($materialId, $structureId)
    {
        $dynamicSchema = new Dynamic();
        if ($structureId == $dynamicSchema->getStructureId()) {

            // If binding material and structure don't exists then create it
            if (
            $this->query->className('structurematerial')
                ->cond('StructureID', $structureId)
                ->cond('MaterialID', $materialId)
                ->count() == 0
            ) {
                // Create relations
                $sm = new structurematerial(false);
                $sm->StructureID = $structureId;
                $sm->MaterialID = $materialId;
                $sm->save();

                // Add row
                m('material_table')->__async_add($materialId, $structureId);
            }
        }
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

        // If is the main material then remove not used tabs
        if (\samsoncms\seo\schema\Schema::getMainSchema()->getStructure()->MaterialID == $entity->id) {

            // Remove all previous tabs
            $form->tabs = array();

            // Added info tab for showing info about this module
            $form->tabs[] = new InfoTab($this);
        }

        // Execute migrations
        $migrate->migrate();

        $tab = new \samsoncms\seo\tab\Tab($renderer, $query, $entity, 'seo_field_tab');
        $tab->setName('SEO');
        $form->tabs[] = $tab;

        $tab = new \samsoncms\seo\tab\Tab($renderer, $query, $entity, 'site_map_field_tab');
        $tab->setName('Site Map');
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
    public function show()
    {

        // Class for work with data
        $display = new Display($this->query);

        // Get main material
        $mainMaterial = $display->getNestedMaterial(Schema::getMainSchema()->getStructure());

        // Iterate all reserved schemas and output their data
        $html = '';
        foreach (Schema::getMaterialSchema() as $schema) {

            // Get current material
            $material = $display->getMaterialByUrl($display->getItemUrl());

            // Exclude publisher
            if ($schema instanceof \samsoncms\seo\schema\material\Publisher) {

                continue;
            }

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

        // Get current material
        $material = $display->getMaterialByUrl($display->getItemUrl());

        $dynamic = new Dynamic();

        $table = null;
        if (!empty($material)) {

            // Get all possible values of tags
            $table = $material->getTable($dynamic->getStructureId());
        }

        // If there not any values then find them in main material
        if (empty($table)) {

            $table = $mainMaterial->getTable($dynamic->getStructureId());
        }

        $html .= '<!-- dynamic tags -->';

        // Get full view
        foreach ($table as $tag) {

            $html .= $this->view($dynamic->view)->content(array_shift($tag))->output();
        }



        // Get all view of not assigned(single) material
        $html .= $display->getCommonViews($this);

        // Show result
        return $this->view($this->viewIndex)->content($html)->output();
    }

    /**
     * Handle core main template rendered event to
     * add SEO needed localization metatags to HTML markup
     * @param string $html Rendered HTML template from core
     * @param array $parameters Collection of data passed to current view
     * @param Module $module Pointer to active core module
     */
    public function templateRenderer(&$html, &$parameters, &$module)
    {
        $content = $this->show();
        $html = str_ireplace('</head>', $content.'</head>', $html);
    }
}
