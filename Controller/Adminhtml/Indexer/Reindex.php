<?php
namespace Magebuddy\ReindexFromAdmin\Controller\Adminhtml\Indexer;

class Reindex extends \Magento\Backend\App\Action
{
    protected $_indexerFactory;
    protected $_logger;
    
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context  $context
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_indexerFactory = $indexerFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $indexerIds = $this->getRequest()->getParam('indexer_ids');
        if (!is_array($indexerIds)) {
            $this->messageManager->addErrorMessage(__('Please select indexers.'));
        } else {
            try {
                foreach ($indexerIds as $indexerId) {
                    $indexer = $this->_indexerFactory->create();
                    $indexer->load($indexerId);
                    $indexer->reindexAll();
                }
                
                $this->messageManager->addSuccessMessage(__('%1 Indexer(s) has been reindexed successfully', count($indexerIds)));//phpcs:ignore
            } catch (\Exception $e) {
                $this->_logger->critical($e);
                $this->messageManager->addErrorMessage(__('We couldn\'t reindex because of an error.'));
            }
        }

        $this->_redirect('indexer/indexer/list');
    }
}