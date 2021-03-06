<?php
/**
 * @package ShopStoreSiteaccess
 * @author  Serhey Dolgushev <dolgushev.serhey@gmail.com>
 * @date    18 Dec 2012
 **/

class shopStoreSiteaccessType extends eZWorkflowEventType
{
	const TYPE_ID = 'shopstoresiteaccess';

	public function __construct() {
		$this->eZWorkflowEventType(
			self::TYPE_ID,
			ezpI18n::tr( 'extension/shop_store_siteaccess', 'Store used siteaccess' )
		);
		$this->setTriggerTypes(
			array(
				'shop' => array(
					'confirmorder' => array( 'before' )
				)
			)
		);
	}

	public function execute( $process, $event ) {
		$parameters = $process->attribute( 'parameter_list' );
		if( isset( $parameters['order_id'] ) === false ) {
			return eZWorkflowEventType::STATUS_ACCEPTED;
		}
		$order = eZOrder::fetch( $parameters['order_id'] );
		if( $order instanceof eZOrder === false ) {
			return eZWorkflowEventType::STATUS_ACCEPTED;
		}

		$check = eZOrderItem::fetchListByType( $order->attribute( 'id' ), 'siteaccess' );
		if( count( $check ) > 0 ) {
			return eZWorkflowEventType::STATUS_ACCEPTED;
		}

		$orderItem = new eZOrderItem(
			array(
				'order_id'        => $order->attribute( 'id' ),
				'description'     => $GLOBALS['eZCurrentAccess']['name'],
				'price'           => 0,
				'type'            => 'siteaccess',
				'vat_is_included' => true,
				'vat_type_id'     => 1
			)
		);
		$orderItem->store();

		return eZWorkflowType::STATUS_ACCEPTED;
	}
}

eZWorkflowEventType::registerEventType(
	shopStoreSiteaccessType::TYPE_ID,
	'shopStoreSiteaccessType'
);
?>
