<?php
namespace MG\VnPay\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutSubmitAllAfterObserver implements ObserverInterface
{
    protected $_invoiceService;
    protected $_transactionFactory;

    public function __construct(
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory
    ) {
        $this->_invoiceService = $invoiceService;
        $this->_transactionFactory = $transactionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        try {
            if(!$order->canInvoice()) {
                return null;
            }
            if(!$order->getState() == 'new') {
                return null;
            }

            $invoice = $this->_invoiceService->prepareInvoice($order);
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();

            $transaction = $this->_transactionFactory->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder());

            $transaction->save();

        } catch (\Exception $e) {
            $order->addStatusHistoryComment('Exception message: '.$e->getMessage(), false);
            $order->save();
            return null;
        }
    }
}