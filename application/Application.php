<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 29.09.2015
 * Time: 11:32
 */

namespace samsoncms\seo\application;

use samsonphp\event\Event;

if (class_exists('\samsoncms\Application', false)) {

    class Application extends \samsoncms\Application
    {
        /** Application name */
        public $name = 'SEO';

        /** Application description */
        public $description = 'Seo module';

        /** Identifier */
        protected $id = 'seo_app';

        /** @var string $icon Icon class */
        public $icon = 'group';

        /**
         * Universal controller action.
         * Entity collection rendering
         */
        public function __handler()
        {
            // Find id main material of main structure
            $structureID = \samsoncms\seo\schema\Schema::getMainSchema()->getStructureId();
            $material = null;
            if (
            $this->query->className('structure')
                ->cond('StructureID', $structureID)
                ->first($material)
            ) {

                // Get id
                $materialID = $material->MaterialID;

                // Redirect to seo module
                url()->redirect("cms/material/form/$materialID#seo_field_tab");

            } else {
                //throw new \Exception('Main material of seo module not found');
                trace('Main material of seo module not found', 1);
            }
        }
    }
}
