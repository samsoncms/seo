<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 26.05.2015
 * Time: 17:08
 */

namespace samsoncms\seo\application;

use samsonframework\core\RenderInterface;
use samsonframework\orm\QueryInterface;
use samsonframework\orm\Record;
use samsonphp\event\Event;

class Form extends \samsoncms\form\Form
{
    public $indexView = 'www/form';

    /**
     * @param RenderInterface $renderer
     * @param QueryInterface $query
     * @param Record $entity
     */
    public function __construct(RenderInterface $renderer, QueryInterface $query, Record $entity)
    {
        // Set module renderer for this tab
        $this->renderer = m('seo');

        // Set query object for this tab
        $this->query = $query;

        // Set db entity of this tab
        $this->entity = $entity;

        // If form tabs are not configured
        if (!sizeof($this->tabs)) {
            // Add MainTab to form tabs
            $this->tabs = array(
                new Entity($renderer, $query, $entity)
            );
        }

        // Fire new event after creating form tabs
        Event::fire('samsoncms.form.created', array(& $this));
    }
}
