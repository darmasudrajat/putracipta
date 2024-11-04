<?php

namespace App\Config;

class UserMenuConfig
{
    public static function getMenuHierarchy(): array
    {
        return [
            'MENU_DASHBOARD' => [],
                      
            
            'MENU_ADMINISTRATION' => ['MENU_USER_MANAGEMENT', 'MENU_TRANSACTION_SETTING'],
            'MENU_MASTER' => [
                'MENU_EMPLOYEE', 'MENU_SUPPLIER', 'MENU_CUSTOMER', 'MENU_PRODUCT', 'MENU_PAPER', 'MENU_MATERIAL', 'MENU_DESIGN_CODE', 'MENU_DIECUT_KNIFE', 
                'MENU_DIELINE_MILLAR', 'MENU_WORK_ORDER_PROCESS', 'MENU_WORK_ORDER_DISTRIBUTION', 'MENU_WORK_ORDER_CHECK_SHEET', 'MENU_MACHINE_PRINTING', 
                'MENU_MATERIAL_CATEGORY', 'MENU_MATERIAL_SUB_CATEGORY', 'MENU_WAREHOUSE', 'MENU_TRANSPORTATION', 'MENU_UNIT', 'MENU_PAYMENT_TYPE', 
                'MENU_CHART_OF_ACCOUNT', 'MENU_ACCOUNT_CATEGORY', 'MENU_DIVISION'
            ],
            'MENU_PURCHASE' => [
                'MENU_PURCHASE_ORDER_MATERIAL', 'MENU_PURCHASE_ORDER_PAPER', 
                'MENU_PURCHASE_INVOICE', 'MENU_PURCHASE_PAYMENT'
            ],
            'MENU_SALE' => ['MENU_SALE_ORDER', 'MENU_SALE_INVOICE', 'MENU_SALE_PAYMENT'],
            'MENU_WAREHOUSE_MATERIAL' => [
                'MENU_PURCHASE_REQUEST_MATERIAL', 'MENU_PURCHASE_REQUEST_PAPER', 'MENU_PURCHASE_RECEIVE', 'MENU_PURCHASE_RETURN', 'MENU_ADJUSTMENT_STOCK', 
                'MENU_MATERIAL_RELEASE', 'MENU_STOCK_TRANSFER'
            ],
            'MENU_WAREHOUSE_FINISHED_GOODS' => ['MENU_SALE_DELIVERY', 'MENU_SALE_RETURN', 'MENU_PRODUCTION_RECEIVE', 'MENU_ADJUSTMENT_STOCK_FINISHED_GOODS', 
                'MENU_STOCK_SALE_ORDER_DETAIL'
            ],
            'MENU_PRODUCTION' => ['MENU_PRODUCT_PROTOTYPE', 'MENU_DEVELOPMENT_PRODUCT', 'MENU_MASTER_ORDER', 'MENU_MATERIAL_REQUEST', 'MENU_QUALITY_CONTROL_SORTING'],
            'MENU_FINANCE' => ['MENU_DEPOSIT', 'MENU_EXPENSE'],
            'MENU_REPORT' => [
                'MENU_REPORT_PURCHASE', 'MENU_REPORT_SALE', 'MENU_REPORT_PRODUCTION', 'MENU_REPORT_WAREHOUSE_MATERIAL', 
                'MENU_REPORT_WAREHOUSE_FINISHED_GOODS', 'MENU_REPORT_FINANCE'
            ],
            'MENU_REPORT_PURCHASE' => ['MENU_REPORT_PURCHASE_ORDER_MATERIAL', 'MENU_REPORT_PURCHASE_ORDER_PAPER', 'MENU_REPORT_MATERIAL_PURCHASE_ORDER',
                'MENU_REPORT_SUPPLIER_PURCHASE_ORDER_MATERIAL', 'MENU_REPORT_PAPER_PURCHASE_ORDER', 'MENU_REPORT_SUPPLIER_PURCHASE_ORDER_PAPER'
            ],
            'MENU_REPORT_SALE' => [
                'MENU_REPORT_SALE_ORDER', 'MENU_REPORT_SALE_ORDER_DETAIL', 'MENU_REPORT_SALE_ORDER_SUMMARY', 'MENU_REPORT_SALE_ORDER_CUSTOMER', 'MENU_REPORT_SALE_ORDER_PRODUCT'
            ],
            'MENU_REPORT_WAREHOUSE_MATERIAL' => [
                'MENU_REPORT_PURCHASE_REQUEST_MATERIAL', 'MENU_REPORT_PURCHASE_REQUEST_PAPER', 'MENU_REPORT_PURCHASE_RECEIVE', 'MENU_REPORT_PURCHASE_RETURN', 
                'MENU_REPORT_ADJUSTMENT_STOCK', 'MENU_REPORT_INVENTORY_RELEASE_MATERIAL', 'MENU_REPORT_INVENTORY_RELEASE_PAPER', 'MENU_REPORT_STOCK_TRANSFER', 
                'MENU_REPORT_INVENTORY_STOCK_MATERIAL', 'MENU_REPORT_INVENTORY_STOCK_SUMMARY_MATERIAL', 'MENU_REPORT_INVENTORY_STOCK_PAPER', 'MENU_REPORT_INVENTORY_STOCK_SUMMARY_PAPER'
            ],
            'MENU_REPORT_WAREHOUSE_FINISHED_GOODS' => [
                'MENU_REPORT_SALE_DELIVERY', 'MENU_REPORT_SALE_RETURN', 'MENU_REPORT_PRODUCTION_RECEIVE', 'MENU_REPORT_PRODUCT_INVENTORY_RECEIVE', 
                'MENU_REPORT_INVENTORY_STOCK_FINISHED_GOODS', 'MENU_REPORT_INVENTORY_STOCK_SUMMARY_PRODUCT'
            ],
            'MENU_REPORT_PRODUCTION' => ['MENU_REPORT_NEW_PRODUCT', 'MENU_REPORT_DEVELOPMENT_PRODUCT', 'MENU_REPORT_MASTER_ORDER', 'MENU_REPORT_QUALITY_CONTROL_SORTING', 
                'MENU_REPORT_DIELINE_MILLAR', 'MENU_REPORT_DESIGN_CODE', 'MENU_REPORT_DIECUT_KNIFE', 'MENU_REPORT_INVENTORY_REQUEST_MATERIAL', 'MENU_REPORT_INVENTORY_REQUEST_PAPER'
            ],
            'MENU_REPORT_FINANCE' => ['MENU_REPORT_DEPOSIT', 'MENU_REPORT_EXPENSE', 'MENU_REPORT_ACCOUNT_RECEIVABLE', 'MENU_REPORT_ACCOUNT_PAYABLE', 
                'MENU_REPORT_SALE_INVOICE', 'MENU_REPORT_SALE_PAYMENT', 'MENU_REPORT_PURCHASE_INVOICE', 'MENU_REPORT_PURCHASE_PAYMENT'
            ],
            'MENU_PROSES_PRODUKSI' => ['MENU_PROSES_PRODUKSI_CETAK'],
            'MENU_TRANSACTION_LOG' => [],
        ];
    }

    public static function getMenuAttributes(): array
    {
        return [
            'MENU_DASHBOARD' => ['route' => 'app_home_page', 'roles' => ['ROLE_USER']],
           
            'MENU_USER_MANAGEMENT' => ['route' => 'app_admin_user_index', 'pattern' => '/^app_admin_user_.+$/', 'roles' => ['ROLE_SETTING']],
            'MENU_TRANSACTION_SETTING' => ['route' => 'app_admin_literal_config_index', 'pattern' => '/^app_admin_literal_config_.+$/', 'roles' => ['ROLE_USER_MANAGEMENT']],
            'MENU_EMPLOYEE' => ['route' => 'app_master_employee_index', 'pattern' => '/^app_master_employee_.+$/', 'roles' => ['ROLE_EMPLOYEE_ADD', 'ROLE_EMPLOYEE_EDIT', 'ROLE_EMPLOYEE_VIEW']],
            'MENU_SUPPLIER' => ['route' => 'app_master_supplier_index', 'pattern' => '/^app_master_supplier_.+$/', 'roles' => ['ROLE_SUPPLIER_ADD', 'ROLE_SUPPLIER_EDIT', 'ROLE_SUPPLIER_VIEW']],
            'MENU_CUSTOMER' => ['route' => 'app_master_customer_index', 'pattern' => '/^app_master_customer_.+$/', 'roles' => ['ROLE_CUSTOMER_ADD', 'ROLE_CUSTOMER_EDIT', 'ROLE_CUSTOMER_VIEW']],
            'MENU_PRODUCT' => ['route' => 'app_master_product_index', 'pattern' => '/^app_master_product_.+$/', 'roles' => ['ROLE_PRODUCT_ADD', 'ROLE_PRODUCT_EDIT', 'ROLE_PRODUCT_VIEW']],
            'MENU_PAPER' => ['route' => 'app_master_paper_index', 'pattern' => '/^app_master_paper_.+$/', 'roles' => ['ROLE_PAPER_ADD', 'ROLE_PAPER_EDIT', 'ROLE_PAPER_VIEW']],
            'MENU_MATERIAL' => ['route' => 'app_master_material_index', 'pattern' => '/^app_master_material_(?!category|sub_category).+$/', 'roles' => ['ROLE_MATERIAL_ADD', 'ROLE_MATERIAL_EDIT', 'ROLE_MATERIAL_VIEW']],
            'MENU_DESIGN_CODE' => ['route' => 'app_master_design_code_index', 'pattern' => '/^app_master_design_code_.+$/', 'roles' => ['ROLE_DESIGN_CODE_ADD', 'ROLE_DESIGN_CODE_EDIT', 'ROLE_DESIGN_CODE_VIEW']],
            'MENU_DIECUT_KNIFE' => ['route' => 'app_master_diecut_knife_index', 'pattern' => '/^app_master_diecut_knife_.+$/', 'roles' => ['ROLE_DIECUT_ADD', 'ROLE_DIECUT_EDIT', 'ROLE_DIECUT_VIEW']],
            'MENU_DIELINE_MILLAR' => ['route' => 'app_master_dieline_millar_index', 'pattern' => '/^app_master_dieline_millar_.+$/', 'roles' => ['ROLE_MILLAR_ADD', 'ROLE_MILLAR_EDIT', 'ROLE_MILLAR_VIEW']],
            'MENU_WORK_ORDER_PROCESS' => ['route' => 'app_master_work_order_process_index', 'pattern' => '/^app_master_work_order_process_.+$/', 'roles' => ['ROLE_PROCESS_ADD', 'ROLE_PROCESS_EDIT', 'ROLE_PROCESS_VIEW']],
            'MENU_WORK_ORDER_DISTRIBUTION' => ['route' => 'app_master_work_order_distribution_index', 'pattern' => '/^app_master_work_order_distribution_.+$/', 'roles' => ['ROLE_DISTRIBUTION_ADD', 'ROLE_DISTRIBUTION_EDIT', 'ROLE_DISTRIBUTION_VIEW']],
            'MENU_WORK_ORDER_CHECK_SHEET' => ['route' => 'app_master_work_order_check_sheet_index', 'pattern' => '/^app_master_work_order_check_sheet_.+$/', 'roles' => ['ROLE_CHECK_SHEET_ADD', 'ROLE_CHECK_SHEET_EDIT', 'ROLE_CHECK_SHEET_VIEW']],
            'MENU_MACHINE_PRINTING' => ['route' => 'app_master_machine_printing_index', 'pattern' => '/^app_master_machine_printing_.+$/', 'roles' => ['ROLE_PRINTING_MACHINE_ADD', 'ROLE_PRINTING_MACHINE_EDIT', 'ROLE_PRINTING_MACHINE_VIEW']],
            'MENU_MATERIAL_CATEGORY' => ['route' => 'app_master_material_category_index', 'pattern' => '/^app_master_material_category_.+$/', 'roles' => ['ROLE_MATERIAL_CATEGORY_ADD', 'ROLE_MATERIAL_CATEGORY_EDIT', 'ROLE_MATERIAL_CATEGORY_VIEW']],
            'MENU_MATERIAL_SUB_CATEGORY' => ['route' => 'app_master_material_sub_category_index', 'pattern' => '/^app_master_material_sub_category_.+$/', 'roles' => ['ROLE_MATERIAL_SUB_CATEGORY_ADD', 'ROLE_MATERIAL_SUB_CATEGORY_EDIT', 'ROLE_MATERIAL_SUB_CATEGORY_VIEW']],
            'MENU_WAREHOUSE' => ['route' => 'app_master_warehouse_index', 'pattern' => '/^app_master_warehouse_.+$/', 'roles' => ['ROLE_WAREHOUSE_ADD', 'ROLE_WAREHOUSE_EDIT', 'ROLE_WAREHOUSE_VIEW']],
            'MENU_TRANSPORTATION' => ['route' => 'app_master_transportation_index', 'pattern' => '/^app_master_transportation_.+$/', 'roles' => ['ROLE_TRANSPORTATION_ADD', 'ROLE_TRANSPORTATION_EDIT', 'ROLE_TRANSPORTATION_VIEW']],
            'MENU_UNIT' => ['route' => 'app_master_unit_index', 'pattern' => '/^app_master_unit_.+$/', 'roles' => ['ROLE_UNIT_ADD', 'ROLE_UNIT_EDIT', 'ROLE_UNIT_VIEW']],
            'MENU_DIVISION' => ['route' => 'app_master_division_index', 'pattern' => '/^app_master_division_.+$/', 'roles' => ['ROLE_DIVISION_ADD', 'ROLE_DIVISION_EDIT', 'ROLE_DIVISION_VIEW']],
            'MENU_PAYMENT_TYPE' => ['route' => 'app_master_payment_type_index', 'pattern' => '/^app_master_payment_type_.+$/', 'roles' => ['ROLE_PAYMENT_TYPE_ADD', 'ROLE_PAYMENT_TYPE_EDIT', 'ROLE_PAYMENT_TYPE_VIEW']],
            'MENU_CHART_OF_ACCOUNT' => ['route' => 'app_master_account_index', 'pattern' => '/^app_master_account_(?!category).+$/', 'roles' => ['ROLE_ACCOUNT_ADD', 'ROLE_ACCOUNT_EDIT', 'ROLE_ACCOUNT_VIEW']],
            'MENU_ACCOUNT_CATEGORY' => ['route' => 'app_master_account_category_index', 'pattern' => '/^app_master_account_category_.+$/', 'roles' => ['ROLE_ACCOUNT_ADD', 'ROLE_ACCOUNT_EDIT', 'ROLE_ACCOUNT_VIEW']],
            'MENU_PURCHASE_REQUEST_MATERIAL' => ['route' => 'app_purchase_purchase_request_header_index', 'pattern' => '/^app_purchase_purchase_request_header_.+$/', 'roles' => ['ROLE_PURCHASE_REQUEST_MATERIAL_ADD', 'ROLE_PURCHASE_REQUEST_MATERIAL_EDIT', 'ROLE_PURCHASE_REQUEST_MATERIAL_VIEW']],
            'MENU_PURCHASE_REQUEST_PAPER' => ['route' => 'app_purchase_purchase_request_paper_header_index', 'pattern' => '/^app_purchase_purchase_request_paper_header_.+$/', 'roles' => ['ROLE_PURCHASE_REQUEST_PAPER_ADD', 'ROLE_PURCHASE_REQUEST_PAPER_EDIT', 'ROLE_PURCHASE_REQUEST_PAPER_VIEW']],
            'MENU_PURCHASE_ORDER_MATERIAL' => ['route' => 'app_purchase_purchase_order_header_index', 'pattern' => '/^app_purchase_purchase_order_header_.+$/', 'roles' => ['ROLE_PURCHASE_ORDER_MATERIAL_ADD', 'ROLE_PURCHASE_ORDER_MATERIAL_EDIT', 'ROLE_PURCHASE_ORDER_MATERIAL_VIEW']],
            'MENU_PURCHASE_ORDER_PAPER' => ['route' => 'app_purchase_purchase_order_paper_header_index', 'pattern' => '/^app_purchase_purchase_order_paper_header_.+$/', 'roles' => ['ROLE_PURCHASE_ORDER_PAPER_ADD', 'ROLE_PURCHASE_ORDER_PAPER_EDIT', 'ROLE_PURCHASE_ORDER_PAPER_VIEW']],
            'MENU_PURCHASE_INVOICE' => ['route' => 'app_purchase_purchase_invoice_header_index', 'pattern' => '/^app_purchase_purchase_invoice_header_.+$/', 'roles' => ['ROLE_PURCHASE_INVOICE_ADD', 'ROLE_PURCHASE_INVOICE_EDIT', 'ROLE_PURCHASE_INVOICE_VIEW']],
            'MENU_PURCHASE_PAYMENT' => ['route' => 'app_purchase_purchase_payment_header_index', 'pattern' => '/^app_purchase_purchase_payment_header_.+$/', 'roles' => ['ROLE_PURCHASE_PAYMENT_ADD', 'ROLE_PURCHASE_PAYMENT_EDIT', 'ROLE_PURCHASE_PAYMENT_VIEW']],
            'MENU_SALE_ORDER' => ['route' => 'app_sale_sale_order_header_index', 'pattern' => '/^app_sale_sale_order_header_.+$/', 'roles' => ['ROLE_SALE_ORDER_ADD', 'ROLE_SALE_ORDER_EDIT', 'ROLE_SALE_ORDER_VIEW']],
            'MENU_SALE_INVOICE' => ['route' => 'app_sale_sale_invoice_header_index', 'pattern' => '/^app_sale_sale_invoice_header_.+$/', 'roles' => ['ROLE_SALE_INVOICE_ADD', 'ROLE_SALE_INVOICE_EDIT', 'ROLE_SALE_INVOICE_VIEW']],
            'MENU_SALE_PAYMENT' => ['route' => 'app_sale_sale_payment_header_index', 'pattern' => '/^app_sale_sale_payment_header_.+$/', 'roles' => ['ROLE_SALE_PAYMENT_ADD', 'ROLE_SALE_PAYMENT_EDIT', 'ROLE_SALE_PAYMENT_VIEW']],
            'MENU_PURCHASE_RECEIVE' => ['route' => 'app_purchase_receive_header_index', 'pattern' => '/^app_purchase_receive_header_.+$/', 'roles' => ['ROLE_RECEIVE_ADD', 'ROLE_RECEIVE_EDIT', 'ROLE_RECEIVE_VIEW']],
            'MENU_PURCHASE_RETURN' => ['route' => 'app_purchase_purchase_return_header_index', 'pattern' => '/^app_purchase_purchase_return_header_.+$/', 'roles' => ['ROLE_PURCHASE_RETURN_ADD', 'ROLE_PURCHASE_RETURN_EDIT', 'ROLE_PURCHASE_RETURN_VIEW']],
            'MENU_ADJUSTMENT_STOCK' => ['route' => 'app_stock_adjustment_stock_header_index', 'pattern' => '/^app_stock_adjustment_stock_header_.+$/', 'roles' => ['ROLE_ADJUSTMENT_ADD', 'ROLE_ADJUSTMENT_EDIT', 'ROLE_ADJUSTMENT_VIEW']],
            'MENU_ADJUSTMENT_STOCK_FINISHED_GOODS' => ['route' => 'app_stock_adjustment_stock_finished_goods_header_index', 'pattern' => '/^app_stock_adjustment_stock_finished_goods_header_.+$/', 'roles' => ['ROLE_ADJUSTMENT_ADD', 'ROLE_ADJUSTMENT_EDIT', 'ROLE_ADJUSTMENT_VIEW']],
            'MENU_MATERIAL_REQUEST' => ['route' => 'app_stock_inventory_request_header_index', 'pattern' => '/^app_stock_inventory_request_header_.+$/', 'roles' => ['ROLE_MATERIAL_REQUEST_ADD', 'ROLE_MATERIAL_REQUEST_EDIT', 'ROLE_MATERIAL_REQUEST_VIEW']],
            'MENU_MATERIAL_RELEASE' => ['route' => 'app_stock_inventory_release_header_index', 'pattern' => '/^app_stock_inventory_release_header_.+$/', 'roles' => ['ROLE_MATERIAL_RELEASE_ADD', 'ROLE_MATERIAL_RELEASE_EDIT', 'ROLE_MATERIAL_RELEASE_VIEW']],
            'MENU_STOCK_TRANSFER' => ['route' => 'app_stock_stock_transfer_header_index', 'pattern' => '/^app_stock_stock_transfer_header_.+$/', 'roles' => ['ROLE_TRANSFER_ADD', 'ROLE_TRANSFER_EDIT', 'ROLE_TRANSFER_VIEW']],
            'MENU_STOCK_SALE_ORDER_DETAIL' => ['route' => 'app_stock_stock_sale_order_detail_index', 'pattern' => '/^app_stock_stock_sale_order_detail_.+$/', 'roles' => ['ROLE_DELIVERY_ADD', 'ROLE_DELIVERY_EDIT', 'ROLE_DELIVERY_VIEW']],
            'MENU_SALE_DELIVERY' => ['route' => 'app_sale_delivery_header_index', 'pattern' => '/^app_sale_delivery_header_.+$/', 'roles' => ['ROLE_DELIVERY_ADD', 'ROLE_DELIVERY_EDIT', 'ROLE_DELIVERY_VIEW']],
            'MENU_SALE_RETURN' => ['route' => 'app_sale_sale_return_header_index', 'pattern' => '/^app_sale_sale_return_header_.+$/', 'roles' => ['ROLE_SALE_RETURN_ADD', 'ROLE_SALE_RETURN_EDIT', 'ROLE_SALE_RETURN_VIEW']],
            'MENU_PRODUCTION_RECEIVE' => ['route' => 'app_stock_inventory_product_receive_header_index', 'pattern' => '/^app_stock_inventory_product_receive_header_.+$/', 'roles' => ['ROLE_FINISHED_GOODS_RECEIVE_ADD', 'ROLE_FINISHED_GOODS_RECEIVE_EDIT', 'ROLE_FINISHED_GOODS_RECEIVE_VIEW']],
            'MENU_PRODUCT_PROTOTYPE' => ['route' => 'app_production_product_prototype_index', 'pattern' => '/^app_production_product_prototype_.+$/', 'roles' => ['ROLE_NEW_PRODUCT_ADD', 'ROLE_NEW_PRODUCT_EDIT', 'ROLE_NEW_PRODUCT_VIEW']],
            'MENU_DEVELOPMENT_PRODUCT' => ['route' => 'app_production_product_development_index', 'pattern' => '/^app_production_product_development_.+$/', 'roles' => ['ROLE_DEVELOPMENT_PRODUCT_ADD', 'ROLE_DEVELOPMENT_PRODUCT_EDIT', 'ROLE_DEVELOPMENT_PRODUCT_VIEW']],
            'MENU_MASTER_ORDER' => ['route' => 'app_production_master_order_header_index', 'pattern' => '/^app_production_master_order_header_.+$/', 'roles' => ['ROLE_MASTER_ORDER_ADD', 'ROLE_MASTER_ORDER_EDIT', 'ROLE_MASTER_ORDER_VIEW']],
            'MENU_QUALITY_CONTROL_SORTING' => ['route' => 'app_production_quality_control_sorting_header_index', 'pattern' => '/^app_production_quality_control_sorting_header_.+$/', 'roles' => ['ROLE_QUALITY_CONTROL_SORTING_ADD', 'ROLE_QUALITY_CONTROL_SORTING_EDIT', 'ROLE_QUALITY_CONTROL_SORTING_VIEW']],
            'MENU_DEPOSIT' => ['route' => 'app_accounting_deposit_header_index', 'pattern' => '/^app_accounting_deposit_header_.+$/', 'roles' => ['ROLE_EXPENSE_ADD', 'ROLE_EXPENSE_EDIT', 'ROLE_EXPENSE_VIEW']],
            'MENU_EXPENSE' => ['route' => 'app_accounting_expense_header_index', 'pattern' => '/^app_accounting_expense_header_.+$/', 'roles' => ['ROLE_DEPOSIT_ADD', 'ROLE_DEPOSIT_EDIT', 'ROLE_DEPOSIT_VIEW']],
            'MENU_REPORT_PURCHASE_REQUEST_MATERIAL' => ['route' => 'app_report_purchase_request_header_index', 'pattern' => '/^app_report_purchase_request_header_.+$/', 'roles' => ['ROLE_PURCHASE_REPORT']],
            'MENU_REPORT_PURCHASE_REQUEST_PAPER' => ['route' => 'app_report_purchase_request_paper_header_index', 'pattern' => '/^app_report_purchase_request_paper_header_.+$/', 'roles' => ['ROLE_PURCHASE_REPORT']],
            'MENU_REPORT_PURCHASE_ORDER_MATERIAL' => ['route' => 'app_report_purchase_order_header_index', 'pattern' => '/^app_report_purchase_order_header_.+$/', 'roles' => ['ROLE_PURCHASE_REPORT']],
            'MENU_REPORT_PURCHASE_ORDER_PAPER' => ['route' => 'app_report_purchase_order_paper_header_index', 'pattern' => '/^app_report_purchase_order_paper_header_.+$/', 'roles' => ['ROLE_PURCHASE_REPORT']],
            'MENU_REPORT_SUPPLIER_PURCHASE_ORDER_MATERIAL' => ['route' => 'app_report_supplier_purchase_order_material_index', 'pattern' => '/^app_report_supplier_purchase_order_material_.+$/', 'roles' => ['ROLE_PURCHASE_REPORT']],
            'MENU_REPORT_SUPPLIER_PURCHASE_ORDER_PAPER' => ['route' => 'app_report_supplier_purchase_order_paper_index', 'pattern' => '/^app_report_supplier_purchase_order_paper_.+$/', 'roles' => ['ROLE_PURCHASE_REPORT']],
            'MENU_REPORT_MATERIAL_PURCHASE_ORDER' => ['route' => 'app_report_material_purchase_order_index', 'pattern' => '/^app_report_material_purchase_order_.+$/', 'roles' => ['ROLE_PURCHASE_REPORT']],
            'MENU_REPORT_PAPER_PURCHASE_ORDER' => ['route' => 'app_report_paper_purchase_order_index', 'pattern' => '/^app_report_paper_purchase_order_.+$/', 'roles' => ['ROLE_PURCHASE_REPORT']],
            'MENU_REPORT_PURCHASE_INVOICE' => ['route' => 'app_report_purchase_invoice_header_index', 'pattern' => '/^app_report_purchase_invoice_header_.+$/', 'roles' => ['ROLE_PURCHASE_REPORT']],
            'MENU_REPORT_PURCHASE_PAYMENT' => ['route' => 'app_report_purchase_payment_header_index', 'pattern' => '/^app_report_purchase_payment_header_.+$/', 'roles' => ['ROLE_PURCHASE_REPORT']],
            'MENU_REPORT_SALE_ORDER' => ['route' => 'app_report_sale_order_header_index', 'pattern' => '/^app_report_sale_order_header_.+$/', 'roles' => ['ROLE_SALE_REPORT']],
            'MENU_REPORT_SALE_ORDER_DETAIL' => ['route' => 'app_report_sale_order_detail_index', 'pattern' => '/^app_report_sale_order_detail_.+$/', 'roles' => ['ROLE_SALE_REPORT']],
            'MENU_REPORT_SALE_ORDER_SUMMARY' => ['route' => 'app_report_sale_order_summary_index', 'pattern' => '/^app_report_sale_order_summary_.+$/', 'roles' => ['ROLE_SALE_REPORT']],
            'MENU_REPORT_SALE_ORDER_CUSTOMER' => ['route' => 'app_report_customer_sale_order_index', 'pattern' => '/^app_report_customer_sale_order_.+$/', 'roles' => ['ROLE_SALE_REPORT']],
            'MENU_REPORT_SALE_ORDER_PRODUCT' => ['route' => 'app_report_product_sale_order_index', 'pattern' => '/^app_report_product_sale_order_.+$/', 'roles' => ['ROLE_SALE_REPORT']],
            'MENU_REPORT_SALE_INVOICE' => ['route' => 'app_report_sale_invoice_header_index', 'pattern' => '/^app_report_sale_invoice_header_.+$/', 'roles' => ['ROLE_SALE_REPORT']],
            'MENU_REPORT_SALE_PAYMENT' => ['route' => 'app_report_sale_payment_header_index', 'pattern' => '/^app_report_sale_payment_header_.+$/', 'roles' => ['ROLE_SALE_REPORT']],
            'MENU_REPORT_PURCHASE_RECEIVE' => ['route' => 'app_report_receive_header_index', 'pattern' => '/^app_report_receive_header_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_PURCHASE_RETURN' => ['route' => 'app_report_purchase_return_header_index', 'pattern' => '/^app_report_purchase_return_header_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_ADJUSTMENT_STOCK' => ['route' => 'app_report_adjustment_stock_header_index', 'pattern' => '/^app_report_adjustment_stock_header_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_INVENTORY_REQUEST_MATERIAL' => ['route' => 'app_report_inventory_request_material_detail_index', 'pattern' => '/^app_report_inventory_request_material_detail_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_INVENTORY_REQUEST_PAPER' => ['route' => 'app_report_inventory_request_paper_detail_index', 'pattern' => '/^app_report_inventory_request_paper_detail_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_INVENTORY_RELEASE_MATERIAL' => ['route' => 'app_report_inventory_release_material_detail_index', 'pattern' => '/^app_report_inventory_release_material_detail_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_INVENTORY_RELEASE_PAPER' => ['route' => 'app_report_inventory_release_paper_detail_index', 'pattern' => '/^app_report_inventory_release_paper_detail_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_STOCK_TRANSFER' => ['route' => 'app_report_stock_transfer_header_index', 'pattern' => '/^app_report_stock_transfer_header_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_INVENTORY_STOCK_MATERIAL' => ['route' => 'app_report_inventory_stock_material_index', 'pattern' => '/^app_report_inventory_stock_material.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_INVENTORY_STOCK_SUMMARY_MATERIAL' => ['route' => 'app_report_inventory_stock_summary_material_index', 'pattern' => '/^app_report_inventory_stock_summary_material_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_INVENTORY_STOCK_PAPER' => ['route' => 'app_report_inventory_stock_paper_index', 'pattern' => '/^app_report_inventory_stock_paper_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_INVENTORY_STOCK_SUMMARY_PAPER' => ['route' => 'app_report_inventory_stock_summary_paper_index', 'pattern' => '/^app_report_inventory_stock_summary_paper_.+$/', 'roles' => ['ROLE_INVENTORY_MATERIAL_REPORT']],
            'MENU_REPORT_SALE_DELIVERY' => ['route' => 'app_report_delivery_header_index', 'pattern' => '/^app_report_delivery_header_.+$/', 'roles' => ['ROLE_INVENTORY_FINISHED_GOODS_REPORT']],
            'MENU_REPORT_SALE_RETURN' => ['route' => 'app_report_sale_return_header_index', 'pattern' => '/^app_report_sale_return_header_.+$/', 'roles' => ['ROLE_INVENTORY_FINISHED_GOODS_REPORT']],
            'MENU_REPORT_PRODUCTION_RECEIVE' => ['route' => 'app_report_inventory_product_receive_header_index', 'pattern' => '/^app_report_inventory_product_receive_header_.+$/', 'roles' => ['ROLE_INVENTORY_FINISHED_GOODS_REPORT']],
            'MENU_REPORT_PRODUCT_INVENTORY_RECEIVE' => ['route' => 'app_report_product_inventory_receive_index', 'pattern' => '/^app_report_product_inventory_receive_.+$/', 'roles' => ['ROLE_INVENTORY_FINISHED_GOODS_REPORT']],
            'MENU_REPORT_INVENTORY_STOCK_FINISHED_GOODS' => ['route' => 'app_report_inventory_stock_product_index', 'pattern' => '/^app_report_inventory_stock_product_.+$/', 'roles' => ['ROLE_INVENTORY_FINISHED_GOODS_REPORT']],
            'MENU_REPORT_INVENTORY_STOCK_SUMMARY_PRODUCT' => ['route' => 'app_report_inventory_stock_summary_product_index', 'pattern' => '/^app_report_inventory_stock_summary_product_.+$/', 'roles' => ['ROLE_INVENTORY_FINISHED_GOODS_REPORT']],
            'MENU_REPORT_NEW_PRODUCT' => ['route' => 'app_report_product_prototype_index', 'pattern' => '/^app_report_product_prototype_.+$/', 'roles' => ['ROLE_PRODUCTION_REPORT']],
            'MENU_REPORT_DEVELOPMENT_PRODUCT' => ['route' => 'app_report_product_development_index', 'pattern' => '/^app_report_product_development_.+$/', 'roles' => ['ROLE_PRODUCTION_REPORT']],
            'MENU_REPORT_MASTER_ORDER' => ['route' => 'app_report_master_order_header_index', 'pattern' => '/^app_report_master_order_header_.+$/', 'roles' => ['ROLE_PRODUCTION_REPORT']],
            'MENU_REPORT_QUALITY_CONTROL_SORTING' => ['route' => 'app_report_quality_control_sorting_index', 'pattern' => '/^app_report_quality_control_sorting_.+$/', 'roles' => ['ROLE_PRODUCTION_REPORT']],
            'MENU_REPORT_DESIGN_CODE' => ['route' => 'app_report_design_code_index', 'pattern' => '/^app_report_design_code_.+$/', 'roles' => ['ROLE_PRODUCTION_REPORT']],
            'MENU_REPORT_DIELINE_MILLAR' => ['route' => 'app_report_dieline_millar_index', 'pattern' => '/^app_report_dieline_millar_.+$/', 'roles' => ['ROLE_PRODUCTION_REPORT']],
            'MENU_REPORT_DIECUT_KNIFE' => ['route' => 'app_report_diecut_knife_index', 'pattern' => '/^app_report_diecut_knife_.+$/', 'roles' => ['ROLE_PRODUCTION_REPORT']],
            'MENU_REPORT_DEPOSIT' => ['route' => 'app_report_deposit_header_index', 'pattern' => '/^app_report_deposit_header_.+$/', 'roles' => ['ROLE_FINANCE_REPORT']],
            'MENU_REPORT_EXPENSE' => ['route' => 'app_report_expense_header_index', 'pattern' => '/^app_report_expense_header_.+$/', 'roles' => ['ROLE_FINANCE_REPORT']],
            'MENU_REPORT_ACCOUNT_RECEIVABLE' => ['route' => 'app_report_customer_receivable_summary_index', 'pattern' => '/^app_report_customer_receivable_summary_.+$/', 'roles' => ['ROLE_FINANCE_REPORT']],
            'MENU_REPORT_ACCOUNT_PAYABLE' => ['route' => 'app_report_supplier_payable_summary_index', 'pattern' => '/^app_report_supplier_payable_summary_.+$/', 'roles' => ['ROLE_FINANCE_REPORT']],
            
            
            'MENU_PROSES_PRODUKSI_CETAK' => ['route' => 'app_hasil_cetak_index', 'pattern' => '/^app_hasil_cetak_index_.+$/', 'roles' => ['ROLE_USER']],


            'MENU_TRANSACTION_LOG' => ['route' => 'app_report_transaction_log_index', 'roles' => ['ROLE_ADMIN']],
        ];
    }
}
