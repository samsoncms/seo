<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 15:00
 */

namespace samsoncms\seo\tab;

use samson\core\SamsonLocale;
use samsoncms\seo\render\Element;
use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;
use samsonframework\orm\Record;
use samsonframework\orm\Relation;

if (class_exists('\samsoncms\app\material\form\tab\LocaleTab', false)) {

    class SeoLocaleTab extends \samsoncms\app\material\form\tab\LocaleTab
    {

        public $headerIndexView = 'form/tab/header/sub';
        public $contentView = 'form/tab/main/sub_content';

        protected $id = 'sub_field_tab';

        /** @var string Tab locale */
        protected $locale = '';

        /** @inheritdoc */
        public function __construct(
            RenderInterface $renderer,
            QueryInterface $query,
            Record $entity,
            $locale = SamsonLocale::DEF,
            $schema = null
        ) {
            $this->locale = $locale;

            $this->schema = $schema;
            // Set name and id of module
            if ($locale != '') {
                $this->id .= '-' . $this->locale;
                $this->name = $this->locale;
            } else {
                $this->name = 'all';
            }

            // Call parent constructor to define all class fields
            parent::__construct($renderer, $query, $entity);
        }

        /** @inheritdoc */
        public function content()
        {
            // Iterate locale and save their generic and data
            $view = '';

//            // Render elements if exists
//            $isElements = isset($this->schema->elements)&&(!empty($this->schema->elements));
//            if ($isElements) {
//
//                // Create element instance
//                $elements = new Element();
//
//                // Render elements of control tab
//                $contentNestedElement = $elements->renderNotNestedElements($this->schema->elements);
//
//                $view = $contentNestedElement;
//            }

            foreach ($this->additionalFields as $fieldID => $additionalField) {

                $content = '';
                // If this field is empty go further
                if (empty($additionalField)) {
                    continue;
                }


                // Render elements if exists
                $isElements = isset($this->schema->elements)&&(!empty($this->schema->elements));
                if ($isElements) {

                    // Create element instance
                    $elements = new Element();

                    foreach ($this->schema->elements as $element) {

                        if (isset($element['Field']) && ($element['Field'] == $additionalField->name)) {

                            // Render elements of control tab
                            $contentNestedElement = $elements->renderNestedElements(array($element), true);

                            // Insert element as first child of table
                            $content .= $contentNestedElement;
                        }
                    }
                }

                // Render field header
                $content .= '<div class="template-form-input-group seo-block">'
                    . $additionalField->renderHeader($this->renderer);

                // Render field content
                $content .= $additionalField->render($this->renderer, $this->query, $this->materialFields[$fieldID])
                    . '</div>';

                $view .= $content;
            }

            // Render tab content
            $content = $this->renderer->view("form/tab/content/fields")
                ->fields($view)
                ->matId($this->entity->id)
                ->output();

            return $this->renderer->view($this->contentView)
                ->content($content)
                ->subTabID($this->id)
                ->output();
        }
    }
}
