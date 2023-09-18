<?php

namespace Aventi\OrderComment\Controller\Index;


class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        parent::__construct($context);
        $this->cart = $cart;
        $this->cartRepository = $cartRepository;
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {

            $comment = trim(str_replace(['comment=','+'],' ',file_get_contents('php://input')));
            $cartId = $this->cart->getQuote()->getId();
            if(is_numeric($cartId) && $comment != ''){
                $comment = preg_replace('/[^a-zA-Z0-9 -]/m', '', $comment);
                $quote = $this->cartRepository->get($cartId);
                $quote->setData('aventi_comment',substr($comment,0,199));
                $this->cartRepository->save($quote);
            }
            return $this->jsonResponse('ok');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse('ok');
        } catch (\Exception $e) {
            $this->logger->error($e);
            return $this->jsonResponse($comment);
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}

