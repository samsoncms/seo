<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 14:39
 */

namespace samsoncms\seo\tab;

use samson\activerecord\dbRelation;
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
        protected $name = 'SEO';

        protected $id = 'seo_field_tab';

        /** @inheritdoc */
        public function __construct(RenderInterface $renderer, QueryInterface $query, Record $entity)
        {
            $this->show = false;

            // Get all main schemas
            $schemasToRender = Schema::getAllSchemas();

            // If this is the main material of seo module then output single schemas
            $mainStructure = Schema::getMainSchema()->getStructure();

            // This material which rendered is nested material of main structure
            $isMainMaterial = $mainStructure->MaterialID == $entity->id;

            // Get structures and fill sub tabs
            foreach ($schemasToRender as $st) {

                $schema = $st;
                // If is the default schema
                if (in_array('samsoncms\seo\schema\material\MaterialSchema', class_implements($schema))) {
                    $this->renderDefaultStructure($renderer, $query, $entity, $st);
                }

                // If is the structure schema and this material which rendered is nested material of main structure
                if (in_array(
                    'samsoncms\seo\schema\structure\StructureSchema',
                    class_implements($st)
                ) and ($isMainMaterial)
                ) {

                    $this->renderDefaultStructure($renderer, $query, $entity, $st);
                }
            }

            $this->show = true;

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

        /** @inheritdoc */
        public function content()
        {
            // Render all sub-tabs contents
            $content = '';
            foreach ($this->subTabs as $subTab) {
                $content .= $subTab->content();
            }

            // Render tab main content
            return $this->renderer->view($this->contentView)->content($content)->output();
        }
    }

} else {
    class Tab
    {
    }
}
