<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 14:39
 */

namespace samson\cms\seo\tab;

use samson\activerecord\dbRelation;
use samson\cms\seo\schema\Schema;
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

            // Get structures
            foreach (Schema::getSchemas() as $structure) {

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
