<?php
class Manthan_Marketplace_Model_Pdf_Invoice extends Mage_Core_Model_Abstract {
   
   protected function _construct() {
        $this->_init('marketplace/pdf_invoice');
    }
	/**
     * Insert logo to pdf page
     *
     * @param Zend_Pdf_Page $page
     * @param null $store
     */
    protected function insertLogo(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = Mage::getStoreConfig('sales/identity/logo', $store);
		
        if ($image) {
            $image = Mage::getBaseDir('media') . '/sales/store/logo/' . $image;
			
            if (is_file($image)) {
				
                $image       = Zend_Pdf_Image::imageWithPath($image);
                $top         = 830; //top border of the page
                $widthLimit  = 270; //half of the page width
                $heightLimit = 270; //assuming the image is not a "skyscraper"
                $width       = $image->getPixelWidth();
                $height      = $image->getPixelHeight();
			
	
                //preserving aspect ratio (proportions)
                $ratio = $width / $height;
                if ($ratio > 1 && $width > $widthLimit) {
                    $width  = $widthLimit;
                    $height = $width / $ratio;
                } elseif ($ratio < 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $height * $ratio;
                } elseif ($ratio == 1 && $height > $heightLimit) {
                    $height = $heightLimit;
                    $width  = $widthLimit;
                }

                $y1 = $top - $height;
                 $y2 = $top;
                 $x1 = 25;
                 $x2 = $x1 + $width;
                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 10;
            }
        }
    }
	public function getPdf($invoice){
		
	$order = $invoice->getOrder();	
	$pdf = new Zend_Pdf();
	$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
	
	$this->insertLogo($page, $invoice->getStore());
	
	$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES_ROMAN);
			
		define('FONT_SIZE'      , 11);
		define('MARGIN_LEFT'    , 40);
		define('MARGIN_TOP'     , 40);
		define('COL_WIDTH'      , 100);
		define('COL_LEFT_MARGIN', 10);
		define('COL_SEPARATOR'  , '|');
		define('ROW_HEIGHT'     , 20);

	$row = 1;
	$x = MARGIN_LEFT;
	$top = $this->y;
	$page->setFont($font, FONT_SIZE);
     
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0.45));
		$page->setLineColor(new Zend_Pdf_Color_GrayScale(0.45));
		$page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
    

      $incrementId = $order->getInvoiceCollection()->getFirstItem()->getIncrementId();
	  
		$page->drawText(Mage::helper('sales')->__('Invoice #%1$s', $incrementId), 35, ($top -= 15), 'UTF-8');
		$page->drawText(Mage::helper('sales')->__('Order #%1$s',$order->getRealOrderId()),35,($top -= 18),'UTF-8');
		$page->drawText( 
			Mage::helper('sales')->__('Order Date: ') . Mage::helper('core')->formatDate(
             $order->getCreatedAtStoreDate(), 'medium', false
            )
		,35,($top -= 16),'UTF-8');

		$top -= 10;
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, ($top - 25));
        $page->drawRectangle(275, $top, 570, ($top - 25));
	  
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $page->drawText(Mage::helper('sales')->__('Sold to:'), 35, ($top - 15), 'UTF-8');
		
		
			   $paymentInfo = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true)
            ->toPdf();
		
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key=>$value){
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);
		
		if (!$order->getIsVirtual()) {
			$page->drawText(Mage::helper('sales')->__('Ship to:'), 285, ($top - 15), 'UTF-8');
		}else{
			$page->drawText(Mage::helper('sales')->__('Payment Method:'), 285, ($top - 15), 'UTF-8'); 
		
		}
		$billingAddress = $this->_formatAddress($order->getBillingAddress()->format('pdf'));
		
		$shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
		$addressesHeight = $this->_calcAddressHeight($billingAddress);
		 
		 $page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, ($top - 25), 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y = $top - 40;
        $addressesStartY = $this->y;
		
		if ($order->getIsVirtual()) {
			 foreach ($payment as $value){
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
                   // $top -= 15;
					}
				}
			}
		}
		
		 foreach ($billingAddress as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags($part), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }
		
		if (!$order->getIsVirtual()) {
		$this->y = $addressesStartY;
            foreach ($shippingAddress as $value){
                if ($value!=='') {
                    $text = array();
                    foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }
			$addressesEndY = $this->y;
			$addressesEndY = min($addressesEndY, $this->y);
			$this->y = $addressesEndY;
			 $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y-25);
            $page->drawRectangle(275, $this->y, 570, $this->y-25);
			 $this->y -= 15;
            $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		
			$page->drawText(Mage::helper('sales')->__('Payment Method'), 35, $this->y, 'UTF-8');
			$page->drawText(Mage::helper('sales')->__('Shipping Method'), 285, $this->y , 'UTF-8');
			 $this->y -=10;
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
			$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		}
		$paymentLeft = 35;
		$yPayments   = $this->y - 15;
			
		if (!$order->getIsVirtual()) {
			 foreach ($payment as $value){
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach (Mage::helper('core/string')->str_split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
					}
				}
			}
		}
		$topMargin    = 0;
		$methodStartY = $this->y;
		$this->y     -= 15;

		 if (!$order->getIsVirtual()) 
		 {
					/* Shipping Address */
			$shippingAddress = $this->_formatAddress($order->getShippingAddress()->format('pdf'));
			$shippingDescription  = $order->getShippingDescription();
            $totalShippingChargesText =  Mage::helper('sales')->__($shippingDescription);
			foreach (Mage::helper('core/string')->str_split($totalShippingChargesText, 55, true, true) as $_value) 
			{
				$page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
				$this->y -= 15;
			}
			$this->y-=10;
			$shippingPrice = null;
			if($order->getShippingMethod() == 'marketplaceproductshipping_marketplaceproductshipping')	
			{
				$shippingPrice = Mage::helper('marketplace')->getSellerPriceFormatAttribute($order,$strong = false, $separator = '<br/>');
				$page->drawText( Mage::helper('sales')->__("(Total Shipping Charges %s )",strip_tags($shippingPrice)), 285, $this->y, 'UTF-8');
			}
			$yShipments = $this->y;
          //  $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
            $yShipments -= $topMargin + 10;
			$currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25,  $methodStartY, 25,  $currentY); //left
            $page->drawLine(25,  $currentY,     570, $currentY); //bottom
            $page->drawLine(570, $currentY,     570, $methodStartY); //right

            $this->y = $currentY;
            $this->y -= 15;
		}	
			
		$page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y -25);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));
		
		$this->y -= 5;
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $page->drawText(Mage::helper('sales')->__('Products'), 30, $this->y, 'UTF-8');
        $page->drawText(Mage::helper('sales')->__('SKU'), 260, $this->y , 'UTF-8');
		$page->drawText(Mage::helper('sales')->__('Price'), 375, $this->y , 'UTF-8');
		$page->drawText(Mage::helper('sales')->__('Qty'), 450, $this->y , 'UTF-8');
		$page->drawText(Mage::helper('sales')->__('Subtotal'), 520, $this->y , 'UTF-8');
		$this->y -= 35;
		
		$subTotal = 0;
		foreach($invoice->getAllItems() as $invoice)
		{ 
			$items = $this->_getItemCollectionFilter($invoice);
			if($items->count() > 0)
			{
				$price = Mage::helper('core')->currency($items->getFirstItem()->getPriceInclTax(), true, false);
				$sub_total = $items->getFirstItem()->getRowTotalInclTax();
				$priceFormatSubTotal  = Mage::helper('core')->currency($sub_total, true, false);
				$page->drawText($items->getFirstItem()->getName(), 30, $this->y, 'UTF-8');
				$this->_getProductOptions($page,$items->getFirstItem()->getProductOptions(),$this->y,$font);
				
				$page->setFont($font, FONT_SIZE);
				$oldY = $this->y;
				
				$this->y = $this->_calcWidthField($page,$items->getFirstItem()->getSku(),$this->y);		
				$page->drawText($price, 375, $oldY, 'UTF-8');
				$page->drawText(round($invoice->getQty(),2), 450, $oldY, 'UTF-8');
				$page->drawText($priceFormatSubTotal, 520, $oldY, 'UTF-8');
				$subTotal +=$sub_total; 
				$this->y -= 35;
			}
		}
		
		$this->y -= 10;
		$page->drawText(Mage::helper('Sales')->__('Subtotal:'),360, $this->y, 'UTF-8');
		$page->drawText(strip_tags($order->formatPrice($subTotal)),520, $this->y, 'UTF-8');
		$grandTotal = $subTotal;
		if($order->getShippingMethod() == 'marketplaceproductshipping_marketplaceproductshipping' && !is_null($shippingPrice))	
		{
			$this->y -= 20;
			$x = 515;
			$grandTotal += Mage::helper('marketplace')->getSellerPriceAttribute($order);
			$page->drawText(Mage::helper('Sales')->__('Total Shipping Charges:'),360, $this->y, 'UTF-8');
			$page->drawText(strip_tags($shippingPrice),525, $this->y, 'UTF-8');
		}
		$this->y -= 20;
		$page->drawText(Mage::helper('Sales')->__('Grand Total:'),360, $this->y, 'UTF-8');
		$page->drawText(strip_tags($order->formatPrice($grandTotal)),520, $this->y, 'UTF-8');
		
		$pdf->pages[] = $page;
		 return $pdf;
	}
	
	protected function _getItemCollectionFilter($invoice)
	{ 
		$sellerId = Mage::getModel('marketplace/seller')->isSeller();
		$itemCollection = Mage::getModel('sales/order_item')->getCollection()
					->addFieldToFilter('item_id',$invoice->getOrderItemId())
					->addFieldToFilter('parent_item_id',array('null'=>true))
					->addFieldToFilter('seller_id',$sellerId);
		return $itemCollection;			
	}
	
	protected function _getProductOptions($page,$options,$y,$font)
	{
		if(isset($options['attributes_info'])){ 
			$page->setFont($font, 8); 
			foreach($options['attributes_info'] as $attribute)
				$page->drawText(  $attribute['label'] . ":" . $attribute['value'] , 35, $y-15, 'UTF-8');		
			}
	}
	protected function _calcWidthField($page,$productField,$y)
	{
		foreach (Mage::helper('core/string')->str_split($productField, 20, true, true) as $_value) 
		{
            $page->drawText($_value, 250, $y, 'UTF-8');
                $y-= 15;
        }
		return $y;
	}
	protected function _calcAddressHeight($address)
    {
        $y = 0;
        foreach ($address as $value){
            if ($value !== '') {
                $text = array();
                foreach (Mage::helper('core/string')->str_split($value, 55, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $y += 15;
                }
            }
        }
        return $y;
    }
	protected function _formatAddress($address)
    {
        $return = array();
        foreach (explode('|', $address) as $str) {
            foreach (Mage::helper('core/string')->str_split($str, 45, true, true) as $part) {
                if (empty($part)) {
                    continue;
                }
                $return[] = $part;
            }
        }
        return $return;
    }
}

?>
