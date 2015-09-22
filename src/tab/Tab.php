<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 14:39
 */

namespace samsoncms\seo\tab;

use samson\activerecord\dbRelation;
use samsoncms\seo\schema\Schema;
use samson\core\SamsonLocale;
use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;
use samsonframework\orm\Record;
use samsonphp\event\Event;

if (class_exists('\samsoncms\form\tab\Generic')) {

    class Tab extends \samsoncms\form\tab\Generic{

        /** @var string Tab name or identifier */
        protected $name = 'SEO';

        protected $id = 'seo_field_tab';

        /** @inheritdoc */
        public function __construct(RenderInterface $renderer, QueryInterface $query, Record $entity)
        {
            $this->show = false;

            $schemasToRender = Schema::getSchemas();

            // If this is the main mateiral of seo module then output single schemas
            if (Schema::getMainSchema()->getStructure()->MaterialID == $entity->id) {

                $schemasToRender = array_merge($schemasToRender, Schema::getSingleSchemas());
            }

            // Get structures
            foreach ($schemasToRender as $structure) {

                // Create child tab
                $subTab = new SeoLocaleTab($renderer, $query, $entity, $structure->getStructureId());

                // Set name of tab
                $subTab->name = ucfirst($structure->id);

                // Load fields
                $subTab->loadAdditionalFields($entity->id, 0, $structure->getStructureId());

                $this->subTabs[] = $subTab;
            }
            $this->show = true;

            // Call parent constructor to define all class fields
            parent::__construct($renderer, $query, $entity);

            // Trigger special additional field
            Event::fire('samsoncms.material.fieldtab.created', array(& $this));
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
    class Tab{}
}
