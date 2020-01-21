<?php

namespace BluePay\Payment\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
	public function upgrade(
		ModuleDataSetupInterface $setup,
		ModuleContextInterface $context
	) {
		$setup->startSetup();
		if (version_compare($context->getVersion(), '1.1.0', '<')) {
			$connection = $setup->getConnection();
			$quote_table = $setup->getTable('quote_payment');
			if ($connection->isTableExists($quote_table) == true) {
				$connection->query("UPDATE quote_payment SET `additional_information` = NULL WHERE `method` = 'bluepay_payment';");
			}
			$payment_table = $setup->getTable('sales_order_payment');
			if ($connection->isTableExists($payment_table) == true) {
				$connection->query("UPDATE sales_order_payment SET `additional_information` = NULL WHERE `method` = 'bluepay_payment';");
			}
		}

		$setup->endSetup();
	}
}