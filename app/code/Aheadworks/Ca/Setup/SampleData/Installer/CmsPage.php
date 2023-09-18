<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ca\Setup\SampleData\Installer;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\Data\PageInterfaceFactory;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\Page;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class CmsPage
 * @package Aheadworks\Ca\Setup\SampleData\Installer
 */
class CmsPage implements SampleDataInstallerInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var PageInterfaceFactory
     */
    private $pageDataFactory;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var string
     */
    private $fileName = 'Aheadworks_Ca::fixtures/cms_pages.csv';

    /**
     * @param Reader $reader
     * @param PageInterfaceFactory $pageDataFactory
     * @param PageRepositoryInterface $pageRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Reader $reader,
        PageInterfaceFactory $pageDataFactory,
        PageRepositoryInterface $pageRepository,
        DataObjectHelper $dataObjectHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->reader = $reader;
        $this->pageDataFactory = $pageDataFactory;
        $this->pageRepository = $pageRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $rows = $this->reader->readFile($this->fileName);
        foreach ($rows as $row) {
            if (!$this->ifExists($row[PageInterface::IDENTIFIER])) {
                $this->createCmsPage($row);
            }
        }
    }

    /**
     * Check if exists
     *
     * @param string $identifier
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function ifExists($identifier)
    {
        $this->searchCriteriaBuilder
            ->addFilter(PageInterface::IDENTIFIER, $identifier)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $pages = $this->pageRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return count($pages) > 0;
    }

    /**
     * Create cms page
     *
     * @param array $row
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function createCmsPage($row)
    {
        /** @var PageInterface|Page $page */
        $page = $this->pageDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $page,
            $row,
            PageInterface::class
        );
        $stores = explode(',', $row['stores']);
        $page->setStoreId($stores);

        $this->pageRepository->save($page);
    }
}
