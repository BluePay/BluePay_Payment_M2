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
		if (version_compare($context->getVersion(), '1.2.0', '<')) {
			$connection = $setup->getConnection();
			$table = $setup->getTable('quote_payment');
			if ($connection->isTableExists($table) == true) {
				$connection->query("UPDATE quote_payment SET `additional_information` = NULL WHERE `method` = 'bluepay_payment';");
			}
		}

		$setup->endSetup();
	}
}