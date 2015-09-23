<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 20.09.2015
 * Time: 15:00
 */

namespace samsoncms\seo\tab;

use samson\activerecord\materialfield;
use samson\core\SamsonLocale;
use samson\pager\Pager;
use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;
use samsonframework\orm\Record;
use samsonframework\orm\Relation;

if (class_exists('\samsoncms\app\material\form\tab\LocaleTab')) {

    class ControlTab extends \samsoncms\app\material\form\tab\LocaleTab{

        public $headerIndexView = 'form/tab/header/sub';
        public $contentView = 'form/tab/main/sub_content';

        protected $id = 'sub_field_tab';

        /** @var string Tab locale */
        protected $locale = '';

        protected $table;

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

        public function fillTable($entity, $query, $structure, $locale)
        {
            //$this->table = new \samson\cms\web\materialtable\MaterialTableTable($entity, $query, $structure, $locale);

            $this->table->query = dbQuery('material')
                ->cond('MaterialID', $entity->id)
                ->order_by('priority')
                ->join('materialfield');

            $this->table->renderModule = s()->module(m('material_table'));

            $fields = null;
            dbQuery('structurefield')->cond('StructureID', $structure->id)->exec($fields);

            foreach ($fields as $field) {
                dbQuery('field')->cond('FieldID', $field->FieldID)->first($field);
                $this->table->headerFields[$field->id] = $field;
            }
        }
        /** @inheritdoc */
        public function content()
        {
            $content = '<div>This is the button</div>';
            return $this->renderer->view($this->contentView)
            ->content($content.$this->table->render())
            ->subTabID($this->id)
            ->output();
        }

        /** @inheritdoc * /
        public function content()
        {
            // Retrieve pointer to current module for rendering
            $renderModule = s()->module(m('material_table'));

            $fieldIds = null;
            dbQuery('structurefield')->cond('StructureID', $this->structure->id)->fields('FieldID', $fieldIds);

            $html = '';
            foreach ($fieldIds as $fieldId) {

                $field = dbQuery('field')->cond('FieldID', $fieldId)->first();
                $materialField = dbQuery('materialfield')->cond('FieldID', $fieldId)->cond('MaterialID', $this->entity->id)->first();

                if (empty($materialField)) {
                    $materialField = new materialfield(false);
                    $materialField->FieldID = $field->FieldID;
                    $materialField->MaterialID = $this->entity->id;
                    $materialField->Active = 1;
                    $materialField->save();
                }

                $input = m('samsoncms_input_application')
                    ->createFieldByType($this->table->dbQuery, $field->Type, $materialField);
                $tdHTML = $renderModule->view('table/tdView')->set($input, 'input')->output();

                $pager = new Pager(0);

                // Render field row
                $html .= $renderModule
                    ->view('table/row')
                    ->set(m('samsoncms_input_text_application')->createField($this->table->dbQuery, $this->entity, 'Url'), 'materialName')
                    ->set('materialID', $this->entity->id)
                    ->set('priority', $this->entity->priority)
                    ->set('parentID', $this->entity->parent_id)
                    ->set('structureId', $this->structure->StructureID)
                    ->set('td_view', $tdHTML)
                    ->set($pager, 'pager')
                    ->output();
            }

            return $this->renderer->view($this->contentView)
                ->content($html)
                ->subTabID($this->id)
                ->output();
        }*/
    }
}
