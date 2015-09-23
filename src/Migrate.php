<?php
/**
 * Created by PhpStorm.
 * User: molodyko
 * Date: 18.09.2015
 * Time: 16:13
 */

namespace samsoncms\seo;

use samsoncms\seo\schema\Main;
use samsoncms\seo\schema\Schema;

/**
 * Class Migrate for create structures in db
 * @package samson\cms\seo
 */
class Migrate
{

    /** @var \samson\activerecord\dbQuery */
    public $query = null;

    /** Type of structure with nested material */
    const NESTED_MATERIAL_TYPE_STRUCTURE = 1;

    const MAIN_PREFIX_NAME = 'main';

    public function __construct($query)
    {
        $this->query = $query;

        // Get all structures
        $this->structures = Schema::getMaterialSchema();

        // Add structures which not assign to material
        $this->structures = array_merge($this->structures, Schema::getStructureSchema());
    }

    /**
     * Execute migrations
     * @throws \Exception
     */
    public function migrate()
    {
        // At first work with main structure
        $main = new Main();

        $mainStructure = $this->isStructureExists($main->structureName, $main->structureUrl);

        // If main structure not exists then create it
        if (!$mainStructure) {

            // Create main structure
            $mainStructure = $this->createStructure($main->structureName, $main->structureUrl);
        }

        // Create and bind all nested field
        //$this->buildFieldsToStructure($main->fields, $mainStructure->id, self::MAIN_PREFIX_NAME);

        // If nested material don't exist then create and assign it
        $this->buildNestedMaterial($mainStructure);

        // Iterate all nested structures and create each of all
        foreach ($this->structures as $schema) {

            $structure = $schema->getStructure();

            // If structure in this schema is already exists then go to the next schema
            if (!$structure) {

                // Create nested structure
                $structure = $this->createStructure(
                    $schema->structureName,
                    $schema->structureUrl,
                    self::NESTED_MATERIAL_TYPE_STRUCTURE,
                    $mainStructure->id
                );
            }

            // Get right fields
            $mainFields = $this->removeNotUsedFields($main->fields, $schema);

            // Assign main fields to structure
            $this->buildFieldsToStructure($mainFields, $structure->id, $schema->id);

            // Assign fields to structure
            $this->buildFieldsToStructure($schema->fields, $structure->id, $schema->id);
        }
    }

    /**
     * Remove fields which not use in relation
     * @param $fields
     * @param $schema
     * @return array
     */
    public function removeNotUsedFields($fields, $schema)
    {
        $rightFields = array();
        foreach ($fields as $field) {

            // If such field found in relation array then save it
            if (in_array($field['Name'], array_flip($schema->relations))) {
                $rightFields[] = $field;
            }
        }

        return $rightFields;
    }

    /**
     * Get nested material in structure
     * @return null
     */
    public function getNestedMaterial($structure)
    {

        // Get nested material
        $material = null;
        $material = dbQuery('material')->cond('MaterialID', $structure->MaterialID)->first();

        return $material;
    }

    /**
     * Create and assign fields to the structure
     * @param $fields
     * @param $structureId
     * @param $prefix
     * @throws \Exception
     */
    public function buildFieldsToStructure($fields, $structureId, $prefix = '')
    {

        // Iterate and create all fields
        foreach ($fields as $field) {

            $fieldInstance = $this->isFieldExists($field['Name'] . '_' . $prefix);

            // If field not exists then create it
            if (!$fieldInstance) {

                trace('create field', 1);
                // Create and add field to structure
                $fieldInstance = $this->createField(
                    $field['Name'] . '_' . $prefix,
                    $field['Description'],
                    $field['Type']
                );
            }


            // If field was created
            if ($fieldInstance) {

                // If field is already exists then go next
                if ($this->isFieldAssigned($fieldInstance->FieldID, $structureId)) {
                    continue;
                }

                trace('assigned field', 1);
                // Add field to structure
                $this->assignFieldToStructure($structureId, $fieldInstance->FieldID);

            } else {
                throw new \Exception('Error when create field');
            }
        }
    }

    /**
     * Create field
     * @param $name
     * @param $description
     * @param $type
     * @return \samson\activerecord\field
     */
    public function createField($name, $description, $type)
    {
        // Save value of field
        $field = new \samson\activerecord\field(false);
        $field->Name = $name;
        $field->Description = $description;
        $field->Type = $type;
        $field->Active = 1;
        $field->save();

        return $field;
    }

    /**
     * Assign field to the structure
     * @param $structureId
     * @param $fieldId
     * @return \samson\activerecord\structurefield
     */
    public function assignFieldToStructure($structureId, $fieldId)
    {
        // Save value of field
        $structureField = new \samson\activerecord\structurefield(false);
        $structureField->StructureID = $structureId;
        $structureField->FieldID = $fieldId;
        $structureField->Active = 1;
        $structureField->save();

        return $structureField;
    }

    /**
     * Create structure and if isset parent structure assign it to them
     * @param $name
     * @param $url
     * @param int $type
     * @param int $parentId
     * @return \samson\activerecord\structure
     */
    public function createStructure($name, $url, $type = self::NESTED_MATERIAL_TYPE_STRUCTURE, $parentId = 0)
    {
        $structure = new \samson\activerecord\structure(false);
        $structure->Name = $name;
        $structure->Url = $url;
        $structure->Active = 1;
        $structure->type = $type;
        $structure->ParentID = $parentId;
        $structure->save();

        // Create structure relation if this structure have to be child
        if ($parentId != 0) {
            $structureRelation = new \samson\activerecord\structure_relation(false);
            $structureRelation->child_id = $structure->StructureID;
            $structureRelation->parent_id = $parentId;
            $structureRelation->save();
        }

        return $structure;
    }

    /**
     * Get structure if exists
     * @param $name
     * @param $url
     * @return mixed
     */
    public function isStructureExists($name, $url)
    {
        return $this->query->className('\samson\cms\Navigation')
            ->cond('Name', $name)
            ->cond('Url', $url)
            ->first();
    }

    /**
     * Get field if exists
     * @param $name
     * @return mixed
     */
    public function isFieldExists($name)
    {
        return $this->query->className('field')
            ->cond('Name', $name)
            ->first();
    }

    /**
     * Get field if exists
     * @param $fieldId
     * @param $structureId
     * @return mixed
     */
    public function isFieldAssigned($fieldId, $structureId)
    {
        return $this->query->className('structurefield')
            ->cond('FieldID', $fieldId)
            ->cond('StructureID', $structureId)
            ->first();
    }

    /**
     * Assign nested material to the structure
     * @param $material
     * @param $structure
     */
    public function assignNestedMaterial($material, $structure)
    {
        // Assign material to structure
        $structureMaterial = new \samson\activerecord\structurematerial(false);
        $structureMaterial->MaterialID = $material->MaterialID;
        $structureMaterial->StructureID = $structure->StructureID;
        $structureMaterial->Active = 1;
        $structureMaterial->save();

        // Update structure field
        $structure->MaterialID = $material->MaterialID;
        $structure->save();
    }

    /**
     * Create nested material of structure
     * @param $name
     * @param $url
     * @return \samson\activerecord\material
     */
    public function createNestedMaterial($name, $url)
    {
        $material = new \samson\activerecord\material(false);
        $material->Name = $name;
        $material->Url = $url;
        $material->Active = 1;
        $material->save();

        return $material;
    }

    /**
     * Create nested material on the structure if it don't exists and assign it to the passed structure
     * @param $structure
     * @return null|\samson\activerecord\material
     */
    public function buildNestedMaterial($structure)
    {

        // Get nested material
        $material = $this->getNestedMaterial($structure);
        if (!$material) {

            // Set prefix of material
            $prefix = 'Material of ';
            $material = $this->createNestedMaterial(
                $prefix . $structure->Name,
                $prefix . $structure->Url
            );

            $this->assignNestedMaterial($material, $structure);

            return $material;
        }

        return null;
    }
}