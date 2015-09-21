<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 15:00
 */

namespace samsoncms\seo\tab;

use samson\core\SamsonLocale;
use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;
use samsonframework\orm\Record;
use samsonframework\orm\Relation;

if (class_exists('\samsoncms\app\material\form\tab\LocaleTab')) {

    class SeoLocaleTab extends \samsoncms\app\material\form\tab\LocaleTab{

        public $headerIndexView = 'form/tab/header/sub';
        public $contentView = 'form/tab/main/sub_content';

        protected $id = 'sub_field_tab';

        /** @var string Tab locale */
        protected $locale = '';

        /** @inheritdoc */
        public function __construct(RenderInterface $renderer, QueryInterface $query, Record $entity, $locale = SamsonLocale::DEF)
        {
            $this->locale = $locale;

            // Set name and id of module
            if ($locale != '') {
                $this->id .= '-'.$this->locale;
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
            foreach ($this->additionalFields as $fieldID => $additionalField) {

                // If this field is empty go further
                if ( empty($additionalField) ) {
                    continue;
                }

                // Render field header
                $view .= '<div class="template-form-input-group seo-block">'.$additionalField->renderHeader($this->renderer);

                // Render field content
                $view .= $additionalField->render($this->renderer, $this->query, $this->materialFields[$fieldID]).'</div>';
            }

            // Render tab content
            $content = $this->renderer->view("form/tab/content/fields")->fields($view)->matId($this->entity->id)->output();

            return $this->renderer->view($this->contentView)
                ->content($content)
                ->subTabID($this->id)
                ->output();
        }
    }
}
