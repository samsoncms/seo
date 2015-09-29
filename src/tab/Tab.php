<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 14:39
 */

namespace samsoncms\seo\tab;

use samson\activerecord\dbRelation;
use samsoncms\seo\render\Element;
use samsoncms\seo\schema\control\ControlSchema;
use samsoncms\seo\schema\material\MaterialSchema;
use samsoncms\seo\schema\Schema;
use samson\core\SamsonLocale;
use samsoncms\seo\schema\structure\StructureSchema;
use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;
use samsonframework\orm\Record;
use samsonphp\event\Event;

if (class_exists('\samsoncms\form\tab\Generic')) {

    class Tab extends \samsoncms\form\tab\Generic
    {

        /** @var string Tab name or identifier */
        protected $name;

        /** @var  String id of tab */
        protected $id;

        /** @inheritdoc */
        public function __construct(RenderInterface $renderer, QueryInterface $query, Record $entity, $tabName)
        {

            // Set id of tab
            $this->id = $tabName;

            // Get all main schemas
            $schemasToRender = Schema::getAllSchemas($this->id);


            // If this is the main material of seo module then output single schemas
            $mainStructure = Schema::getMainSchema()->getStructure();

            // This material which rendered is nested material of main structure
            $isMainMaterial = $mainStructure->MaterialID == $entity->id;

            // Set not showing mode as default
            $this->show = false;

            // Get structures and fill sub tabs
            foreach ($schemasToRender as $st) {

                // If this this schema can be showing on this material
                $allowShowingOnThisMaterial = $isMainMaterial || (!$isMainMaterial && $st->visibility == true);

                // If is the default schema
                if (($st instanceof \samsoncms\seo\schema\material\MaterialSchema) && $allowShowingOnThisMaterial) {

                    $this->renderDefaultStructure($renderer, $query, $entity, $st);

                    // If at least one of the schema can be showing then show tab
                    $this->show = true;
                }

                // If is the control schema and this material which rendered is nested material of main structure
                if (($st instanceof \samsoncms\seo\schema\control\ControlSchema) && $allowShowingOnThisMaterial) {

                    $this->renderControlStructure($renderer, $query, $entity, $st);

                    // If at least one of the schema can be showing then show tab
                    $this->show = true;
                }
            }

            // Call parent constructor to define all class fields
            parent::__construct($renderer, $query, $entity);

            // Trigger special additional field
            Event::fire('samsoncms.material.fieldtab.created', array(& $this));
        }

        /**
         * Render default tab with fields
         * @param $renderer
         * @param $query
         * @param $entity
         * @param $schema
         */
        public function renderDefaultStructure($renderer, $query, $entity, $schema)
        {
            // Create child tab
            $subTab = new SeoLocaleTab($renderer, $query, $entity, $schema->getStructureId());

            // Set name of tab
            $subTab->name = ucfirst($schema->id);

            // Load fields
            $subTab->loadAdditionalFields($entity->id, 0, $schema->getStructureId());

            $this->subTabs[] = $subTab;
        }

        /**
         * Render control schemas
         */
        public function renderControlStructure($renderer, $query, $entity, $schema)
        {
            $structure = $schema->getStructure();

            $structure = dbQuery('\samson\cms\Navigation')->cond('StructureID', $structure->id)->first();

            $subTab = new \samson\cms\web\materialtable\tab\MaterialTableLocalized(
                m('material_table'),
                $query,
                $entity,
                $structure,
                ''
            );

            // Set name of tab
            $subTab->name = ucfirst($schema->id);

            $subTab->schema = $schema;

            $this->subTabs[] = $subTab;
        }

        /** @inheritdoc */
        public function content()
        {
            // Render all sub-tabs contents
            $content = '';
            foreach ($this->subTabs as $subTab) {

                // Get content of table tab
                $html = $subTab->content();

                $isElements = isset($subTab->schema->elements)&&(!empty($subTab->schema->elements));
                if ($isElements) {

                    // Create element instance
                    $elements = new Element();

                    // Render elements of control tab
                    $contentNestedElement = $elements->renderNestedElements($subTab->schema->elements);

                    // Insert element as first child of table
                    $html = preg_replace('/>/', '>' . $contentNestedElement, $html, 1);

                    // Get all not nested element
                    $contentNotNestedElement = $elements->renderNotNestedElements($subTab->schema->elements);
                }

                $module = m('material_table');
                $content .= $module->view('form/tab/main/content')->content($html)->output();

                if ($isElements) {

                    // Concatenate element to main view
                    $content = $contentNotNestedElement . $content;
                }
            }

            // Render tab main content
            return $this->renderer->view($this->contentView)->content($content)->output();
        }

        public function setName($name)
        {
            $this->name = $name;
        }
    }

} else {
    class Tab
    {
    }
}
