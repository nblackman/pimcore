<?php 
/**
 * Pimcore
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.pimcore.org/license
 *
 * @category   Pimcore
 * @package    Object_Class
 * @copyright  Copyright (c) 2009-2010 elements.at New Media Solutions GmbH (http://www.elements.at)
 * @license    http://www.pimcore.org/license     New BSD License
 */

class Object_Objectbrick extends Pimcore_Model_Abstract {
    
    public $items = array();
    public $fieldname;

    protected $__object = null;
    protected $brickGetters = array();

    public function __construct ($object, $fieldname) {
        $this->__object = $object;
        if($fieldname) {
            $this->setFieldname($fieldname);
        }
    }
    
    public function getItems () {
        if(empty($this->items)) {
            foreach(get_object_vars($this) as $var) {
                if($var instanceof Object_Objectbrick_Data_Abstract) {
                    $this->items[] = $var;
                }
            }
        }
        return $this->items;
    }
    
    public function setItems ($items) {
        $this->items = $items;
    }
    
    public function getFieldname () {
        return $this->fieldname;
    }
    
    public function setFieldname ($fieldname) {
        $this->fieldname = $fieldname;
    }

    public function getBrickGetters() {
        $getters = array();
        foreach($this->brickGetters as $bg) {
            $getters[] = "get" . ucfirst($bg);
        }
        return $getters;
    }
    
    public function getItemDefinitions () {
        $definitions = array();
        foreach ($this->getItems() as $item) {
            $definitions[$item->getType()] = $item->getDefinition();
        }
        
        return $definitions;
    }
    
    public function save ($object) {

        $getters = $this->getBrickGetters();
        foreach($getters as $getter) {
            $brick = $this->$getter();
            if($brick instanceof Object_Objectbrick_Data_Abstract) {
                if($brick->getDoDelete()) {
                    $brick->delete($object);
                } else  {
                    $brick->setFieldname($this->getFieldname());
                    $brick->save($object);
                }

            } else {
//                $parent = Object_Service::hasInheritableParentObject($object);

                if($brick == null) {
                    $inheritanceModeBackup = Object_Abstract::getGetInheritedValues(); Object_Abstract::setGetInheritedValues(true);
                    $parentBrick = $this->$getter();
                    Object_Abstract::setGetInheritedValues($inheritanceModeBackup);

                    //                    $containerGetter = "get" . ucfirst($this->getFieldname());
//                    $parentContainer = $parent->$containerGetter();

//                    p_r($parentBrick); die("blup");
                    if(!empty($parentBrick)) {
//                        $parentBrick = $parentContainer->$getter();
//                        if(!empty($parentBrick)) {

                            $brickType = "Object_Objectbrick_Data_" . ucfirst($parentBrick->getType());



                            $brick = new $brickType($object);
                            $brick->setFieldname($this->getFieldname());
                            $brick->save($object);
//                        }

                    }


                }

            }


        }

        if(is_array($this->getItems())) {
            foreach ($this->getItems() as $brick) {
                if($brick instanceof Object_Objectbrick_Data_Abstract) {
                    if($brick->getDoDelete()) {
                        $brick->delete($object);
                    } else  {
                        $brick->setFieldname($this->getFieldname());
                        $brick->save($object);
                    }

                }
            }
        }
    }

    public function getObject() {
        return $this->__object;
    }

    public function delete(Object_Concrete $object) {
        if(is_array($this->getItems())) {

            foreach ($this->getItems() as $brick) {
                if($brick instanceof Object_Objectbrick_Data_Abstract) {
                    $brick->delete($object);
                }
            }
        }
    }
    
    
//    public function isEmpty () {
//        if(count($this->getItems()) < 1) {
//            return true;
//        }
//        return false;
//    }
}