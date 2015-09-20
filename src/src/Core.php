<?php

namespace samson\cms\seo;

use samson\activerecord\dbRecord;
use samson\activerecord\structure;
use samson\cms\CMSMaterial;
use samson\cms\seo\Migrate;
use samson\cms\seo\schema\Facebook;
use samson\cms\seo\schema\Main;
use samson\cms\seo\schema\Schema;
use samson\cms\seo\tab\Tab;
use samson\cms\web\navigation\CMSNav;
use samson\core\CompressableService;
use samsonphp\event\Event;

/**
 * Show mata links for social networks
 * For use this module need create structure and write its id to structureId in config this module
 * Then create depend! material and field for store data in this structure
 * And use this <?php m('seo')->render('')?> for insert tags into head tag
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

    public function init( array $params = array() ){

        // Save dbQuery instance
        $this->query = dbQuery('structure');

        // Fire new event after creating form tabs
        Event::subscribe('samsoncms.material.form.created', array($this, 'renderMaterialTab'));
    }

    /**
     * Create structures
     * @return bool
     */
    public function prepare() {

        $this->query = dbQuery('structure');

        $migrate = new \samson\cms\seo\Migrate($this->query);

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
    public function renderMaterialTab(\samsoncms\app\material\form\Form &$form, $renderer, $query, $entity) {

        // Check if this material has the SEO structure
        $mainStructure = new Main();
        if ($this->query
            ->className('structurematerial')
            ->cond('StructureID', $mainStructure->getStructureId())
            ->cond('MaterialID', $entity->id)
            ->first()
        ) {
            $tab = new Tab($renderer, $query, $entity);
            $form->tabs[] = $tab;
        }
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
        foreach (Schema::getSchemas() as $schema) {

            // Get current material
            $material = $display->getMaterialByUrl($display->getItemUrl());

            // Out comment
            $html .= "<!-- {$schema->id} -->";

            // Iterate all relations
            foreach ($schema->relations as $fieldName => $alias) {

                // Find data in hierarchy of structures
                $content = $display->findField($schema, $fieldName, $material);

                // EXCLUDE Render title of page
                if ($schema->id == 'meta' && $fieldName == '__SEO_Title' ) {
                    $html .= $this->view($this->viewTitle)->title($content)->output();
                }

                // Save html view
                $html .= $this->view($schema->view)->name($alias)->content($content)->output()."\n";
            }
        }

        // Show result
        $this->view($this->viewIndex)->content($html);
    }
}
