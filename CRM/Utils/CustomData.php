<?php
/*-------------------------------------------------------+
| SYSTOPIA CUSTOM DATA HELPER -- VERSION 0.1             |
| Copyright (C) 2017 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
| http://www.systopia.de/                                |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/


class CRM_Utils_CustomData {

   protected $ts_domain = NULL;

   public function __construct($ts_domain) {
     $this->ts_domain = $ts_domain;
   }


   /**
    * will take a JSON source file and synchronise the
    * OptionGroup/OptionValue data in the system with
    * those specs
    */
   public function syncOptionGroup($source_file) {
      $data = json_decode(file_get_contents($source_file), TRUE);
      if (empty($data)) {
         throw new Exception("syncOptionGroup::syncOptionGroup: Invalid specs");
      }

      // first: find or create option group
      $this->translateStrings($data);
      $optionGroup = $this->identifyEntity('OptionGroup', $data);
      if (empty($optionGroup)) {
         // create OptionGroup
         $optionGroup = $this->createEntity('OptionGroup', $data);
      } else {
         // update OptionGroup
         $this->updateEntity('OptionGroup', $data, $optionGroup);
      }

      // now run the update for the OptionValues
      $option_group_id = $optionGroup['id'];
      foreach ($data['_values'] as $optionValueSpec) {
         $this->translateStrings($optionValueSpec);
         $optionValueSpec['option_group_id'] = $optionGroup['id'];
         $optionValue = $this->identifyEntity('OptionValue', $optionValueSpec);

         if (empty($optionValue)) {
            // create OptionValue
            $optionValue = $this->createEntity('OptionValue', $optionValueSpec);
         } else {
            // update OptionValue
            $this->updateEntity('OptionValue', $optionValueSpec, $optionValue);
         }
      }
   }


   /**
    * will take a JSON source file and synchronise the
    * CustomGroup/CustomField data in the system with
    * those specs
    */
   public function syncCustomGroup($source_file) {
      $data = json_decode(file_get_contents($source_file), TRUE);
      if (empty($data)) {
         throw new Exception("CRM_Utils_CustomData::syncCustomGroup: Invalid custom specs");
      }

      // first: find or create custom group
      $this->translateStrings($data);
      $customGroup = $this->identifyEntity('CustomGroup', $data);
      if (empty($customGroup)) {
         // create CustomGroup
         $customGroup = $this->createEntity('CustomGroup', $data);
      } else {
         // update CustomGroup
         $this->updateEntity('CustomGroup', $data, $customGroup);
      }

      // now run the update for the CustomFields
      $custom_group_id = $customGroup['id'];
      foreach ($data['_fields'] as $customFieldSpec) {
         $this->translateStrings($customFieldSpec);
         $customFieldSpec['custom_group_id'] = $customGroup['id'];
         if (!empty($customFieldSpec['option_group_id']) && !is_numeric($customFieldSpec['option_group_id'])) {
            // look up custom group id
            $optionGroup = $this->geyEntityID('OptionGroup', array('name' => $customFieldSpec['option_group_id']));
            $customFieldSpec['option_group_id'] = $optionGroup['id'];
         }

         $customField = $this->identifyEntity('CustomField', $customFieldSpec);

         if (empty($customField)) {
            // create CustomField
            $customField = $this->createEntity('CustomField', $customFieldSpec);
         } else {
            // update CustomField
            $this->updateEntity('CustomField', $customFieldSpec, $customField);
         }
      }
   }

   /**
    * return the ID of the given entity (if exists)
    */
   protected function geyEntityID($entity_type, $selector) {
      if (empty($selector)) return NULL;
      $selector['sequential'] = 1;
      $selector['options'] = array('limit' => 2);

      $lookup_result = civicrm_api3($entity_type, 'get', $selector);
      switch ($lookup_result['count']) {
         case 1:
            // found
            return $lookup_result['values'][0];
         default:
            // more than one found
            error_log("bad lookup selector!");
         case 0:
            // not found
            return NULL;
      }
   }

   /**
    * see if a given entity does already exist in the system
    * the $data blob should have a '_lookup' parameter listing the
    * lookup attributes
    */
   protected function identifyEntity($entity_type, $data) {
      $lookup_query = array(
         'sequential' => 1,
         'options'    => array('limit' => 2));

      foreach ($data['_lookup'] as $lookup_key) {
         $lookup_query[$lookup_key] = $data[$lookup_key];
      }

      error_log("LOOKUP {$entity_type}: " . json_encode($lookup_query));
      $lookup_result = civicrm_api3($entity_type, 'get', $lookup_query);
      switch ($lookup_result['count']) {
         case 0:
            // not found
            return NULL;

         case 1:
            // found
            return $lookup_result['values'][0];

         default:
            // bad lookup selector
            error_log("bad lookup selector!");
            return NULL;
      }
   }

   /**
    * create a new entity
    */
   protected function createEntity($entity_type, $data) {
      // first: strip fields starting with '_'
      foreach (array_keys($data) as $field) {
         if (substr($field, 0, 1) == '_') {
            unset($data[$field]);
         }
      }

      // then run query
      error_log("CREATE {$entity_type}: " . json_encode($data));
      return civicrm_api3($entity_type, 'create', $data);
   }

   /**
    * create a new entity
    */
   protected function updateEntity($entity_type, $requested_data, $current_data) {
      $update_query = array();

      // first: identify fields that need to be updated
      foreach ($requested_data as $field => $value) {
         // fields starting with '_' are ignored
         if (substr($field, 0, 1) == '_') {
            continue;
         }

         if (isset($current_data[$field]) && $value != $current_data[$field]) {
            $update_query[$field] = $value;
         }
      }

      // run update if required
      if (!empty($update_query)) {
         $update_query['id'] = $current_data['id'];
         error_log("UPDATE {$entity_type}: " . json_encode($update_query));
         return civicrm_api3($entity_type, 'create', $update_query);
      } else {
         return NULL;
      }
   }

   /**
    * translate all fields that are listed in the _translate list
    */
   protected function translateStrings(&$data) {
      foreach ($data['_translate'] as $translate_key) {
         $value = $data[$translate_key];
         if (is_string($value)) {
            $data[$translate_key] = ts($value, array('domain' => $this->ts_domain));
         }
      }
   }

}
