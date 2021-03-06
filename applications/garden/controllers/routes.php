<?php if (!defined('APPLICATION')) exit();
/*
Copyright 2008, 2009 Mark O'Sullivan
This file is part of Garden.
Garden is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Garden is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Garden.  If not, see <http://www.gnu.org/licenses/>.
Contact Mark O'Sullivan at mark [at] lussumo [dot] com
*/

/**
 * Route Management
 */
class RoutesController extends GardenController {
   
   public $Uses = array('Form');
   public $ReservedRoutes = array('DefaultController', 'Default404', 'DefaultPermission');
   
   public function Index() {
      $this->Permission('Garden.Routes.Manage');
      $this->AddSideMenu('garden/routes');
      if ($this->Head) {
         $this->Head->AddScript('/applications/garden/js/routes.js');
         $this->Head->Title(Translate('Routes'));
      }
         
      // Load all routes from conf
      $this->Routes = Gdn::Config('Routes', array());
      $this->Render();
   }
   
   public function Add() {
      $this->Permission('Garden.Routes.Manage');
      // Use the edit form with no roleid specified.
      $this->View = 'Edit';
      $this->Edit();
   }
   
   public function Edit($RouteIndex = FALSE) {
      $this->Permission('Garden.Routes.Manage');
      $this->AddSideMenu('garden/routes');
      $Routes = Gdn::Config('Routes');
      $this->Route = FALSE;
      if (is_numeric($RouteIndex) && $RouteIndex !== FALSE) {
         $Keys = array_keys($Routes);
         $this->Route = ArrayValue($RouteIndex, $Keys);
      }
      
      $Validation = new Gdn_Validation();
      $ConfigurationModel = new Gdn_ConfigurationModel('Configuration', PATH_CONF . DS . 'config.php', $Validation);
      $ConfigurationModel->SetField(array('Route', 'Target'));
      
      // Set the model on the form.
      $this->Form->SetModel($ConfigurationModel);
      
      // If seeing the form for the first time...
      if (!$this->Form->AuthenticatedPostBack()) {
         // Apply the config settings to the form.
         if ($this->Route !== FALSE)
            $this->Form->SetData(array('Route' => $this->Route, 'Target' => $Routes[$this->Route]));
      } else {
         // Define some validation rules for the fields being saved
         $ConfigurationModel->Validation->ApplyRule('Route', 'Required');
         $ConfigurationModel->Validation->ApplyRule('Target', 'Required');
         
         // Validate & Save
         $FormPostValues = $this->Form->FormValues();
         if (in_array($this->Route, $this->ReservedRoutes))
            $FormPostValues['Route'] = $this->Route;
            
         if ($ConfigurationModel->Validate($FormPostValues)) {
            $Config = Gdn::Factory(Gdn::AliasConfig);
            $Path = PATH_CONF . DS . 'config.php';
            $Config->Load($Path, 'Save');            
            $Config->Set('Routes'.'.'.ArrayValue('Route', $FormPostValues), ArrayValue('Target', $FormPostValues));
            $Config->Save($Path);
            $this->StatusMessage = Translate("The route was saved successfully.");
            if ($this->_DeliveryType == DELIVERY_TYPE_ALL)
               $this->RedirectUrl = Url('garden/routes');
         } else {
            $this->Form->SetValidationResults($ConfigurationModel->ValidationResults());
         }
      }
      
      $this->Render();
   }
   
   public function Delete($RouteIndex = FALSE, $TransientKey = FALSE) {
      $this->Permission('Garden.Routes.Manage');
      $this->DeliveryType(DELIVERY_TYPE_BOOL);
      $Session = Gdn::Session();
      $Routes = Gdn::Config('Routes');
      $Key = FALSE;
      if (is_numeric($RouteIndex) && $RouteIndex !== FALSE) {
         $Keys = array_keys($Routes);
         $Key = ArrayValue($RouteIndex, $Keys);
      }
      
      // If seeing the form for the first time...
      if ($TransientKey !== FALSE
         && $Session->ValidateTransientKey($TransientKey)
         && !in_array($Key, $this->ReservedRoutes)
         && $Key !== FALSE
      ) {
         $Config = Gdn::Factory(Gdn::AliasConfig);
         $Path = PATH_CONF . DS . 'config.php';
         $Config->Load($Path, 'Save');            
         $Config->Remove('Routes'.'.'.$Key);
         $Config->Save($Path);
      }
      
      if ($this->_DeliveryType === DELIVERY_TYPE_ALL)
         Redirect('garden/routes');

      $this->Render();      
   }
   
   
   public function Initialize() {
      parent::Initialize();
      if ($this->Menu)
         $this->Menu->HighlightRoute('/garden/settings');
   }   
}
