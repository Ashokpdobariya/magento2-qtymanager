<?php

namespace Zealousecommerce\Qtymanager\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session as CheckoutSession;

class UpdateQty extends Action
{
    protected $request;

    private   $checkoutSession;

    protected $customerSession;

    protected $groupRepository;

    protected $resourceConnection;

    protected $_context;

    public function __construct(
        \Magento\Framework\App\Request\Http  $request,
        Context                              $context,
        CheckoutSession                      $checkoutSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
        
    ) {
        $this->request         = $request;
        $this->checkoutSession = $checkoutSession;
        $this->cart            = $cart;
        $this->customerSession  = $customerSession;
        $this->groupRepository  = $groupRepository;
        $this->resourceConnection = $resourceConnection;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_context         = $context;
        parent::__construct($context);
    }
    public function execute()
    {
       
        $resultJson      = $this->resultJsonFactory->create();
        $cartData        = $this->getRequest()->getParams();
        $itemId          = $cartData['item_id'];
        $itemQty         = $cartData['qty'];
        $originalprice   = $cartData['price'];
        $sku             = $cartData['sku'];
        $quote           = $this->checkoutSession->getQuote();
        if ($itemQty > 0) {
            $item  = $quote->getItemById($itemId);
            $item->setQty($itemQty);
            $item->save();
            $this->cart->save();
            $quote->collectTotals()->save();
        }
       
      
        return $resultJson->setData(
           ["updatePrice"=> $originalprice,"id"=>$itemId]
        );
    }
    
}
