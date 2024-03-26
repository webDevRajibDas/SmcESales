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
	class ControllerListComponent extends Component
	{

		/**
		 * Used to get all controllers those no need permission setting
		 *
		 * @access public
		 * @return array
		 */
		public function getOptionalConList()
		{
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
		public function getOptionalMethodList()
		{
			$permissionOptionalMethodList = array(
				"Banks" => array('get_month_by_fiscal_year_id')
			);

			return $permissionOptionalMethodList;
		}


		/**
		 * Used to rename mapping controller 
		 *
		 * @access public
		 * @return array
		 */
		public function getMappingConList($controllerName = "")
		{

			/* Name mapping Controller Config start */
			$mappingCon = array(
				"BankAccounts" => "Bank Accounts List",
				"BankBranches" => "Bank Branches List",
				"Banks" => "Bank List",
				"BonusCardTypes" => "Bonus Card Type",
				"BonusCards" => "Bonus Cards",
				"BonusCombinations" => "Bonus Open Combination",
				"BonusCardCalculate" => "Bonus Card Calculate",
				"BonusCardProcess" => "Bonus Card Process",
				"BonusSummeryReport" => "Bonus Summery Report",
				"BonusCardSummeryReports" => "Bonus Card Summery Report",
				"BonusCardPartyReports" => "Bonus Card Party Report(Ors+Ors 25)",
				"BonusCampaigns" => "Bonus Campaign",

				"DistBonusCards" => "Distributor Bonus Cards",
				"DistBonusCombinations" => "Distributor Bonus Open Combination",
				"DistBonusCardCalculate" => "Distributor Bonus Card Calculate",
				"DistBonusCardProcess" => "Distributor Bonus Card Process",
				"DistBonusSummeryReport" => "Distributor Bonus Summery Report",
				"DistBonusCardSummeryReports" => "Distributor Bonus Card Summery Report",

				"Bonuses" => "Bonus List",
				"Brands" => "Brand List",
				"ChallanDetails" => "Challan Details",
				"Challans" => "Challan List",
				"Claims" => "Claim",
				"ClaimsToAso" => "Claim To Aso",
				"Collections" => "Collection List",
				"Contents" => "Content List",
				"CsaMemos" => "CSA Memo List",
				"CommonMacs" => "Common Mac Settings",
				"CurrentInventories" => "Current Inventory",
				"CurrentInventoryReports" => "Currnet Inventory Report",
				"Dashboards" => "Dashboards",
				"DayCloses" => "Day Close History",
				"DepositReports" => "Deposit Report",
				"Deposits" => "Deposit List",
				"DeletedMemos" => "Deleted Memo List",
				"DepositLogs" => "Deposit Audit Trail",
				"CollectionLogs" => "Collection Audit Trail",
				"Designations" => "Designation List",
				"DistDistribuReports" => "District & Division Wise Distribution Report",
				"DBWiseTopSheetReports" => "DB Wise Top Sheet Report",
				"Districts" => "District List",
				"Divisions" => "Division List",
				"DoChallans" => "DO Challans",
				"DoctorQualifications" => "Doctor Qualifications",
				"DoctorTypes" => "Doctor Types",
				"DoctorVisits" => "Doctor Visits",
				"Doctors" => "Doctors",
				"EffectiveCallsBaseWise" => "Effective Call,Outlet Coverage,Session(Base wise)",
				"EffectiveCalls" => "Effective Call,Outlet Coverage,Session(Area wise)",
				"EsalesReports" => "E-Sales Report",
				"FiscalYears" => "Fiscal Years",
				"GiftItems" => "Gift Item List",
				"Institutes" => "Institute List",
				"InventoryAdjustments" => "Inventory Adjustments",
				"InventoryStatuses" => "Inventory Status",
				"LiveSalesTracks" => "Live Tracking Interval",
				"LocationTypes" => "Location Types",
				"MapSalesTracks" => "Map Sales Tracking",
				"MarketPeople" => "Market People",
				"Markets" => "Market List",
				"MarketsTransfers" => "Market Transfer",
				"MeasurementUnits" => "Measurement Units",
				"MemoNotifications" => "Memo Notifications",
				"MemoSettings" => "Memo Settings",
				"Memos" => "Memo List",
				"MessageCategories" => "Message Category",
				"MessageLists" => "Message List",
				"Months" => "Month List",
				"MigSaleReports" => "Mig Sale Reports",
				"NatioanlSaleTargetsAreaWise" => "National Sales target(Area Wise)",
				"NationalSalesReports" => "National Sales Volume and Value Report",
				"NationalTargetEffectiveCallOutletCoverageSessions" => "National Effective Call,Outlet Coverage,Session",
				"NcpChallans" => "NCP Challan",
				"NcpInventories" => "NCP Inventory",
				"NcpProductIssues" => "NCP Product Issue to SO",
				"NcpReturnChallans" => "NCP Challan ASO to CWH",
				"NcpReturnChallansToAso" => "Ncp Return Challan List (SO to ASO)",
				"Offers" => "Offers",
				"OfficePeople" => "Office People",
				"OfficeTypes" => "Office Type",
				"Offices" => "Office List",
				"OpenCombinations" => "Price Open Combination",
				"OpeningBalanceCollections" => "Collection List(Opening)",
				"OpeningBalanceDeposites" => "Deposite List(Opening)",
				"OpeningBalances" => "Opening Balance List",
				"OutletCategories" => "Outlet Category",
				"SelectiveOutlets" => "Selective Outlet",
				"OutletSalesReports" => "Outlet Sales Report",
				"Outlets" => "Outlet List",
				"DistOutlets" => "Distributor Outlets",
				"DistMarkets" => "Distributor Market",
				"DistCurrentInventories" => "Distributor Current Inventory",
				"DistStores" => "Distributor Stores",
				"DistDistributors" => "Distributor",
				"DistRoutes" => "Distributor Route/Beat",
				"DistTsos" => "TSO",
				"DistTsoMappings" => "TSO Mapping",
				"DistSalesRepresentatives" => "Sales Representative",
				/*
					"DistSaleTargets" => "Distributor National Sales Target",
					"DistNatioanlSaleTargetsAreaWise" => "Distributor National Sales Target Area Wise",
					"DistSaleTargetsBaseWise" => "Distributor Sales Target Base Wise",
				*/
				"DistOutletMaps" => "Distributor and Outlet Mapping",
				"DistNotifications" => "Notification Configuration for Distribution Store",
				"DistMemos" => "Distributor Memo List",
				"DistAreaExecutives" => "Distributor Area Executives",
				"DistDistributorAudits" => "Distributor Audits",
				"DistChallans" => "Distributor Challans",
				"DistChallanDetails" => "Distributor Challan Details",
				"DistInventoryAdjustments" => "Distributor Inventory Adjustments",
				"DistSalesAnalysisReports" => "Distributor Sales Analysis Reports",
				"DistInventoryStatementReports" => "Distributor Inventory Statement Report",
				"DistTranInventoryStatementReports" => "Distributor Inventory Statement Report(tran)",
				"DistProjectionAchievementReports" => "Distributor Projection and Achievement Analysis Report",
				"DistProductPrices" => "Distributor Product Price",
				"DistProductCombinations" => "Distributor Product Combination",

				"SpecialProductPrices" => "Hotel & Resturent Product Price",
				"SpecialProductCombinations" => "Hotel & Resturent Product Combination",

				"PieProductSettings" => "Dashboard - Pie Products",
				"ProductCategories" => "Product Category List",
				"ProductGroups" => "Product Group List",
				"ProductCombinations" => "Product Combination List",
				"ProductConvertHistories" => "Product Convert",
				"ProductIssues" => "Product Issue to SO",
				"ProductMeasurements" => "Product Measurements",
				"ProductPrices" => "Product Price List",
				"ProductPricesV2" => "Product Price List(v2)",
				"ProductSalesReports" => "Product Sales Report",
				"ProductSettings" => "Dashboard - Target and Achievement Products",
				"Products" => "Product List",
				"Programs" => "Program (PCHP,BSP,Pink Star Program,Stockist For Injectable,NGO For Injectable)",
				"ProgramOfficerOutletTags" => "Program Officer Tag To Outlet",
				"NotundinPrograms" => "Notundin Programs",
				"ProjectNgoOutlets" => "Project Ngo Outlet",
				"Projects" => "Projects",
				"PromotionMessageLists" => "Promotional Message List",
				"ProxySells" => "Proxy Sell",
				"RecieverOfficePeople" => "Reciever Office People",
				"ReportEsalesSettings" => "Esales Report Setting",
				"ReportProductSettings" => "Dashboard - Stock Report Products",
				"Requisitions" => "DO",
				"ReturnChallans" => "Return Challan ASO to CWH",
				"ReturnChallansToAso" => "Return Challan List (SO to ASO)",
				"SaleTargetsBaseWise" => "Sales target(Base wise)",
				"SaleTargets" => "National Sales Target",
				"SalesAnalysisReports" => "Sales Analysis Report",
				"SalesPeople" => "Sales People",
				"SalesReports" => "Top Sheet Report",
				"SessionTypes" => "Session Type",
				"Sessions" => "Session",
				"StoreTypes" => "Store Type",
				"StockProcesses" => "Stock Process",
				"Stores" => "Store List",
				"SoStockChecks" => "So Stock Check",
				"TargetForOthers" => "Target For Others",
				"TargetForProductSales" => "Target for Product Sales",
				"TargetTypes" => "Target Type List",
				"Targets" => "Target",
				"Territories" => "Territory List",
				"TerritoryPeople" => "Territory People",
				"ThanaTransfers" => "Thana Transfer",
				"Thanas" => "Thana List",
				"TransactionTypes" => "Transaction Types",
				"UserDoctorVisitPlans" => "Doctor Visit Plan",
				"UserTerritoryLists" => "User to Territory List",
				"Users" => "User List",
				"Variants" => "Variant List",
				"VisitPlanLists" => "Visit Plan",
				"VisitedOutlets" => "Visited Outlet",
				"Weeks" => "Weeks",
				"WebCurrentPrices" => "Current Price",
				// "UserGroupPermissions"=>"User Group Permissions", 
				"UserGroups" => "User Groups",
				"SalesDepositMonitor" => "Sales Deposit Monitor",
				"ProgramProviderReports" => "Program Provider Report",
				"BonusCampaignReports" => "Bonus Campaign Report",
				"CreditNotes" => "Credit Note",
				"DistNcpProductIssues" => "Dist Ncp Product Issue",


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
				"OutletCharacteristicReports" => "Outlet Characteristics Report",
				"WeeklyBankDepositionInformation" => "Weekly Bank Deposittion Information Report",
				"ProjectionAchievementReports" => "Projection and Achievement Analysis Report",
				"MarketCharacteristicReports" => "Market Characteristics Report",
				"OutletDeleteBtnHideDateSetting" => "Outlet Delete Btn. Settings",
				"InstrumentTypes" => "Instrument Type Report",
				"ProjectionAchievementComparisons" => "Projection and Achievement Comparison Report",
				"DcrReports" => "DCR Reports",
				"LpcReports" => "LPC Reports",
				"NgoInstituteSaleReports" => "NGO/Institution Sales Report",
				"MaxMinSalesOutletReports" => "Max/Min Sales Outlets Report",
				"QueryOnSalesReports" => "Query on Sales Information Report",
				"CombineQueryOnSalesReports" => "Combine Query on Sales Information Report",
				"ConsolidateStatementOfSales" => "Consolidated Statement of Sale",
				"InventoryStatementReports" => "Inventory Statement Report",
				"SoWiseDetailSales" => "Product Wise Monthly Sales Detail",
				"SoTerritoryReports" => "So Territory Report",
				"OutletVisitInformationReports" => "Outlet Visit Information Report",
				"AreaBatchLotByStocks" => "Area Stock By Batch/Lot Report",
				"CreditCollectionReports" => "Credit Collection Report",
				"TransactionListStocks" => "Transaction List On Stocks Report",
				"TransactionListStockSos" => "Transaction List On Stocks Report(Territory)",
				"NationalStockReports" => "National Stock Report",
				"ProductMaps" => "Product Map",
				"StoreMaps" => "Store Map",
				"UnitMaps" => "Unit Map",
				"DistCommissionReports" => "Distributor Commission Reports",
				"DistDeliveryMen" => "Distributor Delivery Men",
				"DistOrders" => "SR Order",
				"DistSrVisitPlans" => "Sr Visit Plans",
				"Orders" => "Distributor Product Requisition",
				"DistDistributorBalances" => "Distributor Balance Deposits",
				"DistOrderDeliverySchedules" => "Distributor Order Delivery Schedules",
				"Manages" => "Distributor Product Issue",
				"DistSaleTargetMonths" => "Distributor National Sale Targets (Monthly)",
				"DistNatioanlSaleTargetsAreaWiseMonthly" => "Distributor Sales Target Area Wise (Monthly)",
				"DistNatioanlSaleTargetsAreaSrWiseMonthly" => "Distributor Sales Target Area SR Wise (Monthly)",
				"DistSrProductPrices" => "SR Product Price",
				"DistSrProductCombinations" => "SR Product Combinations",
				"DistNationalEcOcMonthlyTargets" => "Distributor National Target EC, OC (Monthly)",
				"DistAreaEcOcMonthlyTargets" => "Distributor Area EC, OC Targets (Monthly)",
				"DistAreaSrEcOcMonthlyTargets" => "Distributor Area SR Wise EC, OC Targets (Monthly)",
				"DistDiscounts" => "SR Sales Disconut",
				"DistDistributorCloses" => "Distributor Close",
				"DistSrRouteMapings" => "Distributor Sr Route/Beat Mapings",
				"DistBalanceTransactionTypes" => "Balance Transaction Types",
				"DistOutletCategories" => "Distributor Outlet Categories",
				"DistDistributorLimits" => "Distributor Limits",
				"DistBonusCardTypes" => "Incentive Affiliation Types",

				"DistReturnChallans" => "Distributor Return Challan",
				"DistSrVisitPlanLists" => "SR Visit Plan List",
				"DistTopSheetReports" => "Distributor Top Sheet Report(Secondary)",
				"DistDbWiseDetailSales" => "Distributor Product Wise Monthly Sales Detail(Secondary)",
				"OrderComparisonReports" => "Distributor Order comparison reports",
				"DistBonusProductIssues" => "Distributor Bonus Product Issues",
				"DmsUsers" => "DB User List",
				"SrUsers" => "SR User List",
				"DistRevenueReports" => "Distributor Revenue Report",
				"DistDashboards" => "Distributor Dashboard",
				"DistMappingInfo" => "Mapping Info",
				"DistQueryOnSalesReports" => "Distributor Query on Sales Info(Delivery)",
				"DistCombineQueryOnSalesReports" => "Distributor Combine Query on Sales Info(Delivery)",
				"MemoEditablePermissions" => "Memo Editable Permissions",
				"CreditMemoTransfers" => "Credit Memo Transfers",
				"DepositEditablePermissions" => "Deposit Editable Permissions",
				"CollectionEditablePermissions" => "Collection Editable Permissions",
				"WeeklySalesReports" => "Weekly Sales Reports",
				"ProductCategoryOrders" => "Product Category Orders for Weekly Sales Report",
				"DistOrderDeliveries" => "SR Order Delivery",
				"OutletGroups" => "Outlet Groups",
				'GroupWiseDiscountBonusPolicies' => "Discount/Bonus Policies",
				"DistOutletGroups" => "Distributor Outlet Groups",
				'DistGroupWiseDiscountBonusPolicies' => "Distributor Discount/Bonus Policies",
				"DistDistributorTransactionReports" => "Distributor Wise Transaction Reports",
				"DistDistributorTransactionByOfficeReports" => "Office Wise Distributor Transaction Reports",
				"DistMarketCharacteristicReports" => "Distributor Market Characteristics Report",
				"DistOutletCharacteristicReports" => "Distributor Outlet Characteristic Report",
				//"DBCommissionReports"=> "DB Commission Reports",
				"ProductSettingsForReports" => "Product Settings For Reports",
				"DistSrLoginReports" => "SR Login Report",
				"DistDcrReports" => "Distributor DCR Report",
				"DistDistributorBalanceSlips" => "Distributor Deposit Slips",
				"DistGiftItems" => "Distributor Gift Item List",
				"PrimaryMemos" => "Primary Memo List",
				"PrimaryMemoReports" => "Primary Memo Report",
				"PrimarySenderReceivers" => "Primary Memo Sender/Reciever",
				"PrimaryProductSalesReports" => "Primary Product Sales Report",
				"SpecialGroups" => "Special Group",
				"SrSpecialGroups" => "Special Group(SR)",
				'CombinationsV2' => 'Product Combination (v2)',
				'DiscountBonusPolicies' => 'Discout Bonus Policy(V2)',
				"FullcareReports" => "Fullcare Report",
				"MacFreeLogs" => "Mac Free Audit Trail",
				"NbrReports" => "NBR Report",
				"MonthWiseProductValueQtyReports" => "Day Wise Product value & volume Report",
				"ProductToProductConverts" => "Product To Product Convert(Area)",
				"SrRevenueReports" => "Sr Wise Revenue and EC Reports",
				"StockOpeningReports" => "Stock Opening Reports",
				"VatReports" => "VAT Report",
				"OtherEmergencyReports" => "Other Emergency Report",
				"DistOtherEmergencyReports" => "Distributor Other Emergency Report",
				"ProductFrequencyReports" => "Product Frequency Reports",
				"DistProductFrequencyReports" => "Distributor Product Frequency Reports",
				"DistCurrentInventoryBalanceLogs" => "Distributor current inventory balance log",
				"DistCurrentInventoryOpenings" => "Distributor opening inventory view",
				"ProductMonths" => "Expire Prduct Validations Month",
				"OrsSalesThorughCardHolders" => "ORS Sales Through Card Holder",
				"SoAttendances" => "So Attendance List",
				"Employees"=> "Employees List",
				"AreaOfficeRequisitionReport"=> "Area Office Requisition Report",
                "StatementTestReports" => "Statement Test Reports"
			);



			/* Name mapping Controller Config End */

			if ($controllerName != "") {
				return array_search($controllerName, $mappingCon);
			}
			return $mappingCon;
		}




		/**
		 * Used to rename mapping controller 
		 *
		 * @access public
		 * @return array
		 */
		public function getMenuOrderConList()
		{

			/* menu_order start */
			$mappingCon = array(
				"no_data",
				"Dashboards",
				"Products", "ProductCategories", "ProductGroups", "ProductCombinations", "ProductPrices", "OpenCombinations", "MeasurementUnits", "Brands", "Variants", "Bonuses", "BonusCombinations", "ProductConvertHistories", "BonusCards", "BonusCardTypes",
				"Challans", "ChallanDetails", "NcpChallans", "ReturnChallans", "NcpReturnChallans", "Requisitions", "DoChallans", "ProductIssues", "NcpProductIssues", "Stores", "StoreTypes", "CurrentInventories", "NcpInventories", "InventoryAdjustments", "Claims", "ClaimsToAso", "OpeningBalances",
				"OpeningBalanceCollections", "OpeningBalanceDeposites", "InventoryStatuses", "TransactionTypes", "GiftItems", "DistGiftItems",
				"SaleTargets", "NatioanlSaleTargetsAreaWise", "SaleTargetsBaseWise", "NationalTargetEffectiveCallOutletCoverageSessions", "EffectiveCalls", "EffectiveCallsBaseWise",
				"MessageLists", "PromotionMessageLists", "MessageCategories",
				"Doctors", "DoctorTypes", "DoctorQualifications", "DoctorVisits", "UserDoctorVisitPlans", "VisitPlanLists", "Sessions", "VisitedOutlets",
				"Divisions", "Districts", "Thanas", "ThanaTransfers", "Markets", "MarketsTransfers", "Territories", "Outlets", "OutletCategories",
				"Programs", "Projects", "ProjectNgoOutlets",
				"Memos", "CsaMemos", "ProxySells",
				"Deposits", "Collections",
				"Users", "UserGroups", "UserGroupPermissions", "FiscalYears", "Months", "Weeks", "Designations", "Institutes", "Offices", "OfficeTypes", "TargetTypes", "CommonMacs",
				"Banks", "BankBranches", "BankAccounts", "UserTerritoryLists", "InstrumentTypes", "OutletDeleteBtnHideDateSetting", "ProductMaps", "StoreMaps", "UnitMaps",
				"CurrentInventoryReports", "SalesReports", "ProductSalesReports", "DepositReports", "NationalSalesReports", "EsalesReports", "OutletSalesReports", "SalesAnalysisReports",
				"DistDistribuReports", "ProgramProviderReports", "SalesDepositMonitor", "ProjectionAchievementReports", "ProjectionAchievementComparisons",	"WeeklyBankDepositionInformation",
				"DcrReports", "LpcReports", "OutletCharacteristicReports", "MarketCharacteristicReports", "NgoInstituteSaleReports", "QueryOnSalesReports", "MaxMinSalesOutletReports", "InventoryStatementReports",
				"SoWiseDetailSales", "SoTerritoryReports", "ConsolidateStatementOfSales", "OutletVisitInformationReports", "AreaBatchLotByStocks", "CreditCollectionReports", "NationalStockReports",
				"DayCloses", "MapSalesTracks",
				"ProductSettings", "PieProductSettings", "ReportProductSettings", "LiveSalesTracks", "ReportEsalesSettings",
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
				/* for Distributor Management System */
				"DistDistributors",
				"DistStores",
				"DistAreaExecutives",
				"DistTsos",
				"DistTsoMappings",
				"DistCurrentInventories",
				"DistRoutes",
				"DistMarkets",
				"DistOutlets",
				"DistOutletMaps",
				"DistSalesRepresentatives",
				"DistChallans",
				"DistMemos",
				"DistDistributorAudits",
				"DistInventoryAdjustments",
				"DistSaleTargets",
				"DistNatioanlSaleTargetsAreaWise",
				"DistSaleTargetsBaseWise",
				"DistNotifications",
				"DistSalesAnalysisReports",
				"DistInventoryStatementReports",
				"DistProjectionAchievementReports",
				"DistCommissionReports",
				"DistDeliveryMen",
				"DistOrders",
				"DistSrVisitPlans",
				"Orders",
				"DistOrderDeliverySchedules",
				"Manages",
				"DistBonusCards",
				"DistSaleTargetMonths",
				"DistDistributorWiseCommissions",

				"DistNatioanlSaleTargetsAreaWiseMonthly",
				"DistNatioanlSaleTargetsAreaSrWiseMonthly",
				"DistSrProductPrices",
				"DistSrProductCombinations",
				"DistNationalEcOcMonthlyTargets",
				"DistAreaEcOcMonthlyTargets",
				"DistAreaSrEcOcMonthlyTargets",
				"DistDiscounts",
				"DistDistributorCloses",
				"DistSrRouteMapings",
				"DistBalanceTransactionTypes",
				"DistOutletCategories",
				"DistDistributorLimits",
				"DistBonusCardTypes",
				"DistReturnChallans",
				"DistSrVisitPlanLists",
				"OrderComparisonReports",
				"DistBonusProductIssues",
				"DmsUsers",
				"SrUsers",
				"DistRevenueReports",
				"MemoEditablePermissions",
				"CreditMemoTransfers",
				"WeeklySalesReports",
				"ProductCategoryOrders",
				"DistOrderDeliveries",
				"OutletGroups",
				"GroupWiseDiscountBonusPolicies",
				"DistOutletGroups",
				"DistGroupWiseDiscountBonusPolicies",
				"DistDistributorTransactionReports",
				"DistDistributorTransactionByOfficeReports",
				//"DBCommissionReports",
				"WeeklySalesReports",
				"ProductSettingsForReports",
				"DistDistributorBalanceSlips",
				"ProductMonths",
				"Employees",
				"AreaOfficeRequisitionReport",
				"StatementTestReports"
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
		public function filterConList($conList)
		{
			$permissionOptionalConList = $this->getOptionalConList();
			$filterConListArr = array_merge(array_diff($conList, $permissionOptionalConList));
			return $filterConListArr;
		}


		/**
		 * Used to rename understandable name the controller 
		 *
		 * @access public
		 * @return array
		 */
		public function renameController($rawsCon)
		{

			$mappingCon = $this->getMappingConList();
			$renameCon = array();
			foreach ($rawsCon as $key => $value) {
				if (array_key_exists($value, $mappingCon)) {
					$renameCon[$key] = $mappingCon[$value];
				} else {
					$renameCon[$key] = $value;
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
		public function get()
		{
			$controllerClasses = App::objects('Controller');
			$superParentActions = get_class_methods('Controller');
			$parentActions = get_class_methods('AppController');
			$parentActionsDefined = $this->_removePrivateActions($parentActions);
			$parentActionsDefined = array_diff($parentActionsDefined, $superParentActions);
			$controllers = array();
			foreach ($controllerClasses as $controller) {
				$controllername = str_replace('Controller', '', $controller);
				$actions = $this->__getControllerMethods($controllername, $superParentActions, $parentActions);
				if (!empty($actions)) {
					$controllers[$controllername] = $actions;
				}
			}
			$plugins = App::objects('plugins');
			foreach ($plugins as $p) {
				$pluginControllerClasses = App::objects($p . '.Controller');
				foreach ($pluginControllerClasses as $controller) {
					$controllername = str_replace('Controller', '', $controller);
					$actions = $this->__getControllerMethods($controllername, $superParentActions, $parentActions, $p);
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
		private function _removePrivateActions($actions)
		{
			foreach ($actions as $k => $v) {
				if ($v{
					0} == '_') {
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
		private function __getControllerMethods($controllername, $superParentActions, $parentActions, $p = null)
		{
			if (empty($p)) {
				App::import('Controller', $controllername);
			} else {
				App::import('Controller', $p . '.' . $controllername);
			}
			$actions = get_class_methods($controllername . "Controller");
			if (!empty($actions)) {
				$actions = $this->_removePrivateActions($actions);
				$actions = ($controllername == 'App') ? array_diff($actions, $superParentActions) : array_diff($actions, $parentActions);
			}
			return $actions;
		}
		/**
		 *  Used to get controller's list
		 *
		 * @access public
		 * @return array
		 */
		public function getControllers(){

			$controllerClasses = App::objects('Controller');
			foreach ($controllerClasses as $key => $value) {
				$controllerClasses[$key] = str_replace('Controller', '', $value);
			}

			//$controllerClasses[-2]="Select Controller";
			//$controllerClasses[-1]="All";

			$plugins = App::objects('plugins');

			foreach ($plugins as $p) {
				$pluginControllerClasses = App::objects($p . '.Controller');
				foreach ($pluginControllerClasses as $controller) {
					$controllerClasses[] = str_replace('Controller', '', $controller);
				}
			}


			$controllerClassesRaws = $this->filterConList($controllerClasses);

			$controllerClasses = $this->renameController($controllerClassesRaws);

			$controllerClasses[-2] = "Select Module";
			//$controllerClasses[-1]="All";       
			ksort($controllerClasses);
			return $controllerClasses;
		}
	}
