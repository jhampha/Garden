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
 * Helps with rendering html elements.
 *
 * @author Mark O'Sullivan
 * @copyright 2003 Mark O'Sullivan
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL
 * @package Garden
 * @version @@GARDEN-VERSION@@
 * @namespace Lussumo.Garden.UI
 */

class Html extends Gdn_Pluggable {

   public static function Anchor($Link, $Text, $Attributes = '') {
      return '<a href="'.Url($Link).'"'.$this->BuildAttributes($Attributes).'>'.Translate($Text).'</a>';
   }

   public static function CancelAnchor($Link, $Text = 'Cancel', $Attributes = '') {
      return $this->Anchor($Link, $Text, $Attributes);
   }

   public static function Image($Url, $Attributes = '') {
      return '<img src="'.Asset($Url).'"'.Attribute($Attributes).' />';
   }
}
?>