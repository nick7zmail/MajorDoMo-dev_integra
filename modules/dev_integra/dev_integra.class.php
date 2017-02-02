<?php
/**
* RF Integra 
* @package project
* @author Wizard <sergejey@gmail.com>
* @copyright http://majordomo.smartliving.ru/ (c)
* @version 0.1 (wizard, 17:02:17 [Feb 02, 2017])
*/
//
//
class dev_integra extends module {
/**
* dev_integra
*
* Module class constructor
*
* @access private
*/
function dev_integra() {
  $this->name="dev_integra";
  $this->title="RF Integra";
  $this->module_category="<#LANG_SECTION_DEVICES#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->data_source)) {
  $p["data_source"]=$this->data_source;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $data_source;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($data_source)) {
   $this->data_source=$data_source;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  if ($this->action=='admin') {
   $this->admin($out);
  } else {
   $this->usual($out);
  }
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['DATA_SOURCE']=$this->data_source;
  $out['TAB']=$this->tab;
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 $this->getConfig();
 $out['API']=$this->config['API'];

 if ($this->view_mode=='update_settings') {
   global $api;
   $this->config['API']=$api;
   $this->saveConfig();
   $this->redirect("?");
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='dev_integra_devices' || $this->data_source=='') {
  if ($this->view_mode=='' || $this->view_mode=='search_dev_integra_devices') {
   $this->search_dev_integra_devices($out);
  }
  if ($this->view_mode=='edit_dev_integra_devices') {
   $this->edit_dev_integra_devices($out, $this->id);
  }
  if ($this->view_mode=='delete_dev_integra_devices') {
   $this->delete_dev_integra_devices($this->id);
   $this->redirect("?data_source=dev_integra_devices");
  }
 }
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='dev_integra_commands') {
  if ($this->view_mode=='' || $this->view_mode=='search_dev_integra_commands') {
   $this->search_dev_integra_commands($out);
  }
  if ($this->view_mode=='edit_dev_integra_commands') {
   $this->edit_dev_integra_commands($out, $this->id);
  }
 }
}
/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* dev_integra_devices search
*
* @access public
*/
 function search_dev_integra_devices(&$out) {
  require(DIR_MODULES.$this->name.'/dev_integra_devices_search.inc.php');
 }
/**
* dev_integra_devices edit/add
*
* @access public
*/
 function edit_dev_integra_devices(&$out, $id) {
  require(DIR_MODULES.$this->name.'/dev_integra_devices_edit.inc.php');
 }
/**
* dev_integra_devices delete record
*
* @access public
*/
 function delete_dev_integra_devices($id) {
  $rec=SQLSelectOne("SELECT * FROM dev_integra_devices WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM dev_integra_devices WHERE ID='".$rec['ID']."'");
 }
/**
* dev_integra_commands search
*
* @access public
*/
 function search_dev_integra_commands(&$out) {
  require(DIR_MODULES.$this->name.'/dev_integra_commands_search.inc.php');
 }
/**
* dev_integra_commands edit/add
*
* @access public
*/
 function edit_dev_integra_commands(&$out, $id) {
  require(DIR_MODULES.$this->name.'/dev_integra_commands_edit.inc.php');
 }
 function propertySetHandle($object, $property, $value) {
  $this->getConfig();
   $table='dev_integra_commands';
   $properties=SQLSelect("SELECT ID FROM $table WHERE LINKED_OBJECT LIKE '".DBSafe($object)."' AND LINKED_PROPERTY LIKE '".DBSafe($property)."'");
   $total=count($properties);
   if ($total) {
    for($i=0;$i<$total;$i++) {
     //to-do
    }
   }
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS dev_integra_devices');
  SQLExec('DROP TABLE IF EXISTS dev_integra_commands');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall() {
/*
dev_integra_devices - 
dev_integra_commands - 
*/
  $data = <<<EOD
 dev_integra_devices: ID int(10) unsigned NOT NULL auto_increment
 dev_integra_devices: TITLE varchar(100) NOT NULL DEFAULT ''
 dev_integra_devices: DEV_TYPE varchar(255) NOT NULL DEFAULT ''
 dev_integra_devices: DEV_CONTROL varchar(255) NOT NULL DEFAULT ''
 dev_integra_devices: UPDATED datetime
 dev_integra_commands: ID int(10) unsigned NOT NULL auto_increment
 dev_integra_commands: TITLE varchar(100) NOT NULL DEFAULT ''
 dev_integra_commands: VALUE varchar(255) NOT NULL DEFAULT ''
 dev_integra_commands: DEVICE_ID int(10) NOT NULL DEFAULT '0'
 dev_integra_commands: LINKED_OBJECT varchar(100) NOT NULL DEFAULT ''
 dev_integra_commands: LINKED_PROPERTY varchar(100) NOT NULL DEFAULT ''
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgRmViIDAyLCAyMDE3IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
