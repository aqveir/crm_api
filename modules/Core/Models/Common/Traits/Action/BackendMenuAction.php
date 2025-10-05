<?php

namespace Modules\Core\Models\Common\Traits\Action;

use Config;
use Modules\Core\Models\Common\BackendMenu;
use Illuminate\Support\Facades\Log;

use Exception;

/**
 * Action methods on BackendMenu
 */
trait BackendMenuAction
{
	/**
	 * Function to generate the Menu Structure with nested
	 * submenus.
	 */
	public function getMenuStucture($key, $filterActive=false)
	{
		$objReturnValue=null;
		try {
			$menu_structure=null;

			//Get Menu Data
			$menus = $this->getMenu($filterActive);

			//Build Menu Structure
			if($menus && count($menus)>0) {
				$menu_structure=[];
				foreach ($menus as $menu) {
					if($menu['parent_id']==0) {
						array_push($menu_structure, $this->getMenuStructure($menu->toArray(), $menus));
					} //End if
				} //Loop ends
			} //End if

            //Get the Backend Menu Object
            $objReturnValue = $menu_structure;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error($e->getMessage());
		} //Try-Catch ends
		return $objReturnValue;
	}
	

	/**
	 * Recurrsive Loop to build the Menu
	 */
	private function getMenuStructure($parentmenu, $menus)
	{
		$objReturnValue=null;
		try {
			foreach ($menus as $menu) {
				if(($menu) && ($parentmenu['id']==$menu['parent_id'])) {
					if(!array_key_exists("submenu",$parentmenu)) {
						$parentmenu['submenu']=[];
					} //End if
					
					array_push($parentmenu['submenu'], $this->getMenuStructure($menu->toArray(), $menus));
				} //End if
			} //Loop ends

            //Get the Parent Menu Object
            $objReturnValue = $parentmenu;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error($e->getMessage());
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends


	/**
	 * Create Backend Menu
	 */
	public function getMenu($filterActive=false)
	{
		$objReturnValue=null;
		try {
			$query = BackendMenu::orderBy('id', 'asc');
			$query = $query->orderBy('parent_id', 'asc');
			$query = $query->orderBy('order', 'asc');

			if($filterActive) {
				$query = $query->where('is_active', '=', 1);
			} //End if

			$query = $query->get();

            //Get the Customer Object
            $objReturnValue = $query;
		} catch (Exception $e) {
			$objReturnValue=null;
			Log::error($e->getMessage());
		} //Try-Catch ends
		return $objReturnValue;
	} //Function ends
	

	/**
	 * Create Backend Menu
	 */
	public function create()
	{

	}
	
	/**
	 * Create Backend Menu
	 */
	public function update()
	{

	}

} //End Class