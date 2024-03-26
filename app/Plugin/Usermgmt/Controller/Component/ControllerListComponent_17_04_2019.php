<?php
/*
	This file is part of UserMgmt.

	Author: Chetan Varshney (http://ektasoftwares.com)

	UserMgmt is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	UserMgmt is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/
class ControllerListComponent extends Component {
         
     /**
	 * Used to get all controllers those no need permission setting
	 *
	 * @access public
	 * @return array
	 */
	public function getOptionalConList() {
		$permissionOptionalConList = array(
			'ApiDataRetrives',
			'ApiDataRetrives050418Ok142v',
			'ApiDataRetrives040220181732',
			'ApiDataRetrives18-01-2018Naser',
			'ApiDataRetrives26022018',
			'ApiDataRetrivesBak280220182018',
			'App',
			'Dashboards280318',
			'Memos123',
			'Memos19072017',
			'Memos210817',
			'Memos27072017',
			'ProductIssues17072017',
			'ApiDataRetrives141Okay',
			'AchievementEffectiveCallOutletCoverage',
			'ClaimDetailsOld',
			'ClaimsOld',
			'DailyOfficewiseProductSalesSummaries',
			'DailySoActivitySummaries',
			'DailyStaffwiseProductSalesSummaries',
			'DamageInventories',
			'DamageReturnChallans',
			'DamageReturnChallansToAso',
			'InventoryStores',
			'MonthlyOfficewiseProductSalesSummaries',
			'Options',
			'Pages',
			'SalesReport',
			'XlsMarketOutlets',
			'FilterApp',
			'UserMgmtApp',
			'UserDoctorVisitPlanLists',
			'UserGroupPermissions',
			);
		return $permissionOptionalConList;
	}



    /**
	 * Used to get all controller's method those no need permission setting
	 *
	 * @access public
	 * @return array
	 */
	public function getOptionalMethodList() {
		$permissionOptionalMethodList=array(
			"Banks"=>array('get_month_by_fiscal_year_id')
			);

		return $permissionOptionalMethodList;
	}


	/**
	 * Used to rename mapping controller 
	 *
	 * @access public
	 * @return array
	 */
	public function getMappingConList($controllerName="") {

		/* Name mapping Controller Config start */
         $mappingCon=array(
         	"BankAccounts"=> "Bank Accounts List",
         	"BankBranches"=> "Bank Branches List",
         	"Banks"=> "Bank List",
         	"BonusCardTypes"=> "Bonus Card Type",
         	"BonusCards"=> "Bonus Cards",
         	"BonusCombinations"=> "Bonus Open Combination",
			"BonusCardCalculate"=>"Bonus Card Calculate",
         	"BonusCardProcess"=>"Bonus Card Process",
         	"BonusSummeryReport"=>"Bonus Summery Report",
         	"BonusCardSummeryReports"=>"Bonus Card Summery Report",
         	"Bonuses"=> "Bonus List",
         	"Brands"=> "Brand List",
         	"ChallanDetails"=>"Challan Details",
         	"Challans"=>"Challan List",
         	"Claims"=>"Claim",
         	"ClaimsToAso"=>"Claim To Aso",
         	"Collections"=>"Collection List",
         	"Contents"=>"Content List",
         	"CsaMemos"=>"CSA Memo List",
         	"CurrentInventories"=>"Current Inventory",
         	"CurrentInventoryReports"=>"Currnet Inventory Report",
         	"Dashboards"=>"Dashboards",
         	"DayCloses"=>"Day Close History",
         	"DepositReports"=>"Deposit Report",
         	"Deposits"=>"Deposit List",
         	"Designations"=>"Designation List",
         	"DistDistribuReports"=>"District & Division Wise Distribution Report",
         	"Districts"=>"District List",
         	"Divisions"=>"Division List",
         	"DoChallans"=>"DO Challans",
         	"DoctorQualifications"=>"Doctor Qualifications",
         	"DoctorTypes"=>"Doctor Types",
         	"DoctorVisits"=>"Doctor Visits",
         	"Doctors"=>"Doctors",
         	"EffectiveCallsBaseWise"=>"Effective Call,Outlet Coverage,Session(Base wise)",
         	"EffectiveCalls"=>"Effective Call,Outlet Coverage,Session(Area wise)",
         	"EsalesReports"=>"E-Sales Report",
         	"FiscalYears"=>"Fiscal Years",
         	"GiftItems"=>"Gift Item List",
         	"Institutes"=>"Institute List",
         	"InventoryAdjustments"=>"Inventory Adjustments",
         	"InventoryStatuses"=>"Inventory Status",
         	"LiveSalesTracks"=>"Live Tracking Interval",
         	"LocationTypes"=>"Location Types",
         	"MapSalesTracks"=>"Map Sales Tracking",
         	"MarketPeople"=>"Market People",
         	"Markets"=>"Market List",
         	"MarketsTransfers"=>"Market Transfer",
         	"MeasurementUnits"=>"Measurement Units",
         	"MemoNotifications"=>"Memo Notifications",
         	"MemoSettings"=>"Memo Settings",
         	"Memos"=>"Memo List",
         	"MessageCategories"=>"Message Category",
         	"MessageLists"=>"Message List",
         	"Months"=>"Month List",
			"MigSaleReports"=>"Mig Sale Reports",
         	"NatioanlSaleTargetsAreaWise"=>"National Sales target(Area Wise)",
         	"NationalSalesReports"=>"National Sales Volume and Value Report",
         	"NationalTargetEffectiveCallOutletCoverageSessions"=>"National Effective Call,Outlet Coverage,Session",
         	"NcpChallans"=>"NCP Challan",
         	"NcpInventories"=>"NCP Inventory",
         	"NcpProductIssues"=>"NCP Product Issue to SO",
         	"NcpReturnChallans"=>"NCP Challan ASO to CWH",
         	"NcpReturnChallansToAso"=>"Ncp Return Challan List (SO to ASO)",
         	"Offers"=>"Offers",
         	"OfficePeople"=>"Office People",
         	"OfficeTypes"=>"Office Type",
         	"Offices"=>"Office List",
         	"OpenCombinations"=>"Price Open Combination",
         	"OpeningBalanceCollections"=>"Collection List(Opening)",
         	"OpeningBalanceDeposites"=>"Deposite List(Opening)",
         	"OpeningBalances"=>"Opening Balance List",
         	"OutletCategories"=>"Outlet Category",
         	"OutletSalesReports"=>"Outlet Sales Report",
         	"Outlets"=>"Outlet List",
         	"PieProductSettings"=>"Dashboard - Pie Products",
         	"ProductCategories"=>"Product Category List",
         	"ProductCombinations"=>"Product Combination List",
         	"ProductConvertHistories"=>"Product Convert",
         	"ProductIssues"=>"Product Issue to SO",
         	"ProductMeasurements"=>"Product Measurements",
         	"ProductPrices"=>"Product Price List",
         	"ProductSalesReports"=>"Product Sales Report",
         	"ProductSettings"=>"Dashboard - Target and Achievement Products",
         	"Products"=>"Product List",
         	"Programs"=>"Program (PCHP,BSP,Pink Star Program,Stockist For Injectable,NGO For Injectable)",
			"NotundinPrograms"=>"Notundin Programs",
         	"ProjectNgoOutlets"=>"Project Ngo Outlet",
         	"Projects"=>"Projects",
         	"PromotionMessageLists"=>"Promotional Message List",
         	"ProxySells"=>"Proxy Sell",
         	"RecieverOfficePeople"=>"Reciever Office People",
         	"ReportEsalesSettings"=>"Esales Report Setting",
         	"ReportProductSettings"=>"Dashboard - Stock Report Products",
         	"Requisitions"=>"DO",
         	"ReturnChallans"=>"Return Challan ASO to CWH",
         	"ReturnChallansToAso"=>"Return Challan List (SO to ASO)",
         	"SaleTargetsBaseWise"=>"Sales target(Base wise)",
         	"SaleTargets"=>"National Sales Target",
         	"SalesAnalysisReports"=>"Sales Analysis Report",
         	"SalesPeople"=>"Sales People",         	
         	"SalesReports"=>"Top Sheet Report",
         	"SessionTypes"=>"Session Type",
         	"Sessions"=>"Session",
         	"StoreTypes"=>"Store Type",
         	"Stores"=>"Store List",
         	"TargetForOthers"=>"Target For Others",
         	"TargetForProductSales"=>"Target for Product Sales",
         	"TargetTypes"=>"Target Type List",
         	"Targets"=>"Target",
         	"Territories"=>"Territory List",
         	"TerritoryPeople"=>"Territory People",
         	"ThanaTransfers"=>"Thana Transfer",
         	"Thanas"=>"Thana List",
         	"TransactionTypes"=>"Transaction Types",
         	"UserDoctorVisitPlans"=>"Doctor Visit Plan",
         	"UserTerritoryLists"=>"User to Territory List",
         	"Users"=>"User List",  
         	"Variants"=>"Variant List",
         	"VisitPlanLists"=>"Visit Plan",
			"VisitedOutlets"=>"Visited Outlet",
         	"Weeks"=>"Weeks",                     	
         	// "UserGroupPermissions"=>"User Group Permissions", 
         	"UserGroups"=>"User Groups",
         	"SalesDepositMonitor"=>"Sales Deposit Monitor",
			"ProgramProviderReports"=>"Program Provider Report",
			
			
			//add new					
			/*"OutletCharacteristicReports"=>"Outlet Characteristics Reports",
			"WeeklyBankDepositionInformation"=>"Weekly Bank Deposittion Information",
			"ProjectionAchievementReports"=>"Projection and Achievement Analysis Report",
			"MarketCharacteristicReports"=>"Market Characteristics Reports",
			"OutletDeleteBtnHideDateSetting"=>"Outlet Delete Button Hide Date",
			"InstrumentTypes"=>"Instrument Type",
			"ProjectionAchievementComparisons"=>"Projection and Achievement Comparison",
			"DcrReports"=>"DCR Reports",
			"NgoInstituteSaleReports"=>"NGO/Institution Sales Report",			
			"MaxMinSalesOutletReports"=>"Max/Min Sales Outlets",
			"QueryOnSalesReports"=>"Query on Sales Information",
			"VisitedOutlets"=>"Visited Outlet",
			"ConsolidateStatementOfSales"=>"Consolidated Statement of Sale",
			"InventoryStatementReports"=>"Inventory Statement Report",
			"SoWiseDetailSales"=>"So Wise Detail Sales",
			"SoTerritoryReports"=>"So Territory Report",
			"OutletVisitInformationReports"=>"Outlet Visit Information Report",*/
			
			
			//add new					
			"OutletCharacteristicReports"=>"Outlet Characteristics Report",
			"WeeklyBankDepositionInformation"=>"Weekly Bank Deposittion Information Report",
			"ProjectionAchievementReports"=>"Projection and Achievement Analysis Report",
			"MarketCharacteristicReports"=>"Market Characteristics Report",
			"OutletDeleteBtnHideDateSetting"=>"Outlet Delete Btn. Settings",
			"InstrumentTypes"=>"Instrument Type Report",
			"ProjectionAchievementComparisons"=>"Projection and Achievement Comparison Report",
			"DcrReports"=>"DCR Reports",
			"NgoInstituteSaleReports"=>"NGO/Institution Sales Report",			
			"MaxMinSalesOutletReports"=>"Max/Min Sales Outlets Report",
			"QueryOnSalesReports"=>"Query on Sales Information Report",			
			"ConsolidateStatementOfSales"=>"Consolidated Statement of Sale",
			"InventoryStatementReports"=>"Inventory Statement Report",
			"SoWiseDetailSales"=>"Product Wise Monthly Sales Detail",
			"SoTerritoryReports"=>"So Territory Report",
			"OutletVisitInformationReports"=>"Outlet Visit Information Report",						
			"AreaBatchLotByStocks"=>"Area Stock By Batch/Lot Report",
			"CreditCollectionReports"=>"Credit Collection Report",
			"TransactionListStocks"=>"Transaction List On Stocks Report", 
			"NationalStockReports"=>"National Stock Report",
			"ProductMaps"=>"Product Map",
			"StoreMaps"=>"Store Map",
			"UnitMaps"=>"Unit Map",
			"Employees"=> "Employees List",
			
         	);



         /* Name mapping Controller Config End */
    
         if($controllerName!="")
         {
         	return array_search($controllerName,$mappingCon);
         }

		return $mappingCon;
	}
	
	
	
	
	/**
	 * Used to rename mapping controller 
	 *
	 * @access public
	 * @return array
	 */
	public function getMenuOrderConList() {

		/* menu_order start */
         $mappingCon=array(
		    "no_data",
		    "Dashboards",
			"Products","ProductCategories","ProductCombinations","ProductPrices","OpenCombinations","MeasurementUnits","Brands","Variants","Bonuses","BonusCombinations","ProductConvertHistories","BonusCards","BonusCardTypes",
			"Challans","ChallanDetails","NcpChallans","ReturnChallans","NcpReturnChallans","Requisitions","DoChallans","ProductIssues","NcpProductIssues","Stores","StoreTypes","CurrentInventories","NcpInventories","InventoryAdjustments","Claims","ClaimsToAso","OpeningBalances",
			     "OpeningBalanceCollections","OpeningBalanceDeposites","InventoryStatuses","TransactionTypes","GiftItems",
			"SaleTargets","NatioanlSaleTargetsAreaWise","SaleTargetsBaseWise","NationalTargetEffectiveCallOutletCoverageSessions","EffectiveCalls","EffectiveCallsBaseWise",
			"MessageLists","PromotionMessageLists","MessageCategories",
			"Doctors","DoctorTypes","DoctorQualifications","DoctorVisits","UserDoctorVisitPlans","VisitPlanLists","Sessions","VisitedOutlets",
			"Divisions","Districts","Thanas","ThanaTransfers","Markets","MarketsTransfers","Territories","Outlets","OutletCategories",
			"Programs","Projects","ProjectNgoOutlets",
			"Memos","CsaMemos","ProxySells",
			"Deposits","Collections",
			"Users","UserGroups","UserGroupPermissions","FiscalYears","Months","Weeks","Designations","Institutes","Offices","OfficeTypes","TargetTypes",
                "Banks","BankBranches","BankAccounts","UserTerritoryLists","InstrumentTypes","OutletDeleteBtnHideDateSetting","ProductMaps","StoreMaps","UnitMaps",			
			"CurrentInventoryReports","SalesReports","ProductSalesReports","DepositReports","NationalSalesReports","EsalesReports","OutletSalesReports","SalesAnalysisReports",
                "DistDistribuReports","ProgramProviderReports","SalesDepositMonitor","ProjectionAchievementReports","ProjectionAchievementComparisons",	"WeeklyBankDepositionInformation",
                "DcrReports","OutletCharacteristicReports","MarketCharacteristicReports","NgoInstituteSaleReports","QueryOnSalesReports","MaxMinSalesOutletReports","InventoryStatementReports",
                 "SoWiseDetailSales","SoTerritoryReports","ConsolidateStatementOfSales","OutletVisitInformationReports","AreaBatchLotByStocks","CreditCollectionReports","NationalStockReports",
            "DayCloses","MapSalesTracks",
            "ProductSettings","PieProductSettings","ReportProductSettings","LiveSalesTracks","ReportEsalesSettings",								      
         	"Contents",         	                  	                 	         	       	       	         	                	
         	"LocationTypes",         	
         	"MarketPeople",         	  
         	"MemoNotifications",
         	"MemoSettings",         	         	         	         	         	         	         	
         	"NcpReturnChallansToAso",
         	"Offers",
         	"OfficePeople",         	     	         	                  	         	         	        		         	                 
         	"ProductMeasurements",         	         	         	                 	            
         	"RecieverOfficePeople",         	         	        
         	"ReturnChallansToAso",         	         	
         	"SalesPeople",         	         	
         	"SessionTypes",         	        
         	"TargetForOthers",
         	"TargetForProductSales",         	
         	"Targets",         	
         	"TerritoryPeople",         	 	                  	         	          	                  	                    	                  																										
			"TransactionListStocks",  
			"NotundinPrograms",	
			"MigSaleReports",
			"Employees"		
         	);


         /* Menu Order End */
             
		return $mappingCon;
	}
	
	
	

	/**
	 * Used to get remove the controller where no need permission
	 *
	 * @access public
	 * @return array
	 */
	public function filterConList($conList) {
		$permissionOptionalConList=$this->getOptionalConList();		
	    $filterConListArr = array_merge(array_diff($conList, $permissionOptionalConList));
        return $filterConListArr;
	}


	/**
	 * Used to rename understandable name the controller 
	 *
	 * @access public
	 * @return array
	 */
	public function renameController($rawsCon) {
          
        $mappingCon=$this->getMappingConList();
		$renameCon=array();		
		foreach ($rawsCon as $key => $value) {
			if (array_key_exists($value,$mappingCon))
			{
            $renameCon[$key]=$mappingCon[$value];
			}
			else 
			{
			$renameCon[$key]=$value;
			}
		}	

        return $renameCon;
	}

	/**
	 * Used to get all controllers with all methods for permissions
	 *
	 * @access public
	 * @return array
	 */
	public function get() {
		$controllerClasses = App::objects('Controller');
		$superParentActions = get_class_methods('Controller');
		$parentActions = get_class_methods('AppController');
		$parentActionsDefined=$this->_removePrivateActions($parentActions);
		$parentActionsDefined = array_diff($parentActionsDefined, $superParentActions);
		$controllers= array();
		foreach ($controllerClasses as $controller) {
			$controllername=str_replace('Controller', '',$controller);
			$actions= $this->__getControllerMethods($controllername, $superParentActions, $parentActions);
			if (!empty($actions)) {
				$controllers[$controllername] = $actions;
			}
		}
		$plugins = App::objects('plugins');
		foreach ($plugins as $p) {
			$pluginControllerClasses = App::objects($p.'.Controller');
			foreach ($pluginControllerClasses as $controller) {
				$controllername=str_replace('Controller', '',$controller);
				$actions= $this->__getControllerMethods($controllername, $superParentActions, $parentActions, $p);
				if (!empty($actions)) {
					$controllers[$controllername] = $actions;
				}
			}
		}
		return $controllers;
	}
	/**
	 * Used to delete private actions from list of controller's methods
	 *
	 * @access private
	 * @param array $actions Controller's action
	 * @return array
	 */
	private function _removePrivateActions($actions) {
		foreach ($actions as $k => $v) {
			if ($v{0} == '_') {
				unset($actions[$k]);
			}
		}
		return $actions;
	}
	/**
	 * Used to get methods of controller
	 *
	 * @access private
	 * @param string $controllername Controller name
	 * @param array $superParentActions Controller class methods
	 * @param array $parentActions App Controller class methods
	 * @param string $p plugin name
	 * @return array
	 */
	private function __getControllerMethods($controllername, $superParentActions, $parentActions, $p=null) {
		if (empty($p)) {
			App::import('Controller', $controllername);
		} else {
			App::import('Controller', $p.'.'.$controllername);
		}
		$actions = get_class_methods($controllername."Controller");
		if (!empty($actions)) {
			$actions=$this->_removePrivateActions($actions);
			$actions= ($controllername=='App') ? array_diff($actions, $superParentActions) : array_diff($actions, $parentActions);
		}
		return $actions;
	}
	/**
	 *  Used to get controller's list
	 *
	 * @access public
	 * @return array
	 */
	public function getControllers() {
		
		$controllerClasses = App::objects('Controller');

		foreach ($controllerClasses as $key=>$value) {
			$controllerClasses[$key]=str_replace('Controller', '',$value);
		}

		//$controllerClasses[-2]="Select Controller";
		//$controllerClasses[-1]="All";

		$plugins = App::objects('plugins');

		foreach ($plugins as $p) {
			$pluginControllerClasses = App::objects($p.'.Controller');
			foreach ($pluginControllerClasses as $controller) {
				$controllerClasses[]=str_replace('Controller', '',$controller);
			}
		}
		
       
        $controllerClassesRaws=$this->filterConList($controllerClasses);

        $controllerClasses=$this->renameController($controllerClassesRaws);

        $controllerClasses[-2]="Select Module";
		//$controllerClasses[-1]="All";       
        ksort($controllerClasses);
		return $controllerClasses;
	}
}