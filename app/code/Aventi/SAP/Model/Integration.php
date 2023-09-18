<?php
/**
 * Copyright © Aventi SAS All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Aventi\SAP\Model;

use Aventi\SAP\Helper\Attribute;
use Aventi\SAP\Logger\Logger;
use Bcn\Component\Json\Reader;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class Integration
{
    private $arrayOption = [];

    /**
     * @var Attribute
     */
    protected $attributeHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var OutputInterface
     */
    private $_output = null;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var false|resource
     */
    private $_file;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * Construct Integration
     * @param Attribute $attribute
     * @param Logger $logger
     * @param DriverInterface $driver
     * @param Filesystem $filesystem
     */
    public function __construct(
        \Aventi\SAP\Helper\Attribute $attribute,
        \Aventi\SAP\Logger\Logger $logger,
        \Magento\Framework\Filesystem\DriverInterface $driver,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->attributeHelper = $attribute;
        $this->logger = $logger;
        $this->driver = $driver;
        $this->fileSystem = $filesystem;
    }

    /**
     * @return OutputInterface|null
     */
    private function getOutput(): ?OutputInterface
    {
        return $this->_output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->_output = $output;
    }

    /**
     * Opens a resource file and returns its instance.
     * @throws FileSystemException
     * @return Reader|boolean
     */
    public function getJsonReader($filePath)
    {
        try {
            if ($this->driver->isExists($filePath)) {
                $this->_file = $this->driver->fileOpen($filePath, 'r');
                return new Reader($this->_file);
            }
        } catch (FileSystemException $e) {
            throw new FileSystemException(__('An error has occurred: ' . $e->getMessage()));
        }
        return false;
    }

    /**
     * Close a resource file and deletes it.
     * @param $file
     * @throws FileSystemException
     */
    public function closeFile($file)
    {
        try {
            $this->driver->fileClose($this->_file);
            $this->driver->deleteFile($file);
        } catch (FileSystemException $e) {
            throw new FileSystemException(__('An error has occurred: ' . $e->getMessage()));
        }
    }

    /**
     * Creates a ProgressBar instance and returns it.
     * @param $total
     * @return null|ProgressBar
     */
    public function startProgressBar($total): ?ProgressBar
    {
        $out = $this->getOutput();
        if ($out) {
            $progressBar = new ProgressBar($out, $total);
            $progressBar->start();
            return $progressBar;
        }
        return null;
    }

    /**
     * @param $progressBar
     */
    public function advanceProgressBar($progressBar)
    {
        if ($this->getOutput()) {
            $progressBar->advance();
        }
    }

    /**
     * Finish the progressBar.
     * @param $progressBar
     * @param $start
     * @param $rows
     */
    public function finishProgressBar($progressBar, $start, $rows)
    {
        $out = $this->getOutput();
        if ($out) {
            $progressBar->finish();
            $out->writeln(sprintf("\nInteraction %s", ($start / $rows)));
        }
    }

    /**
     * Print Table with synchro results.
     * @param $response
     */
    public function printTable($response)
    {
        $out = $this->getOutput();
        if ($out) {
            $out->writeln("\n");
            $table = new Table($out);
            $table->setRows([
                ['Data New', $response['new']],
                ['Data Updated', $response['updated']],
                ['Data Check', $response['check']],
                ['Data Fail', $response['fail']]
            ]);
            $table->render();
        }
    }

    /**
     * Saves the fields with the values to update. The fields
     * can be the Product or Price.
     *
     * @param ProductInterface $item The source (Product or Price) to update.
     * @param array $checkData The data previously checked with the fields.
     */
    public function saveFields(ProductInterface $item, array $checkData)
    {
        foreach ($checkData as $key => $field) {
            $item->setData($key, $field);
            try {
                $item->getResource()->saveAttribute($item, $key);
            } catch (LocalizedException $e) {
                $this->logger->error("Producto murió al guardar esta info: " . json_encode($checkData) . " ---- Error: " . $e->getMessage());
            }
        }
    }

    /**
     * Get or create the option by attributes and returns id.
     *
     * @param string $label
     * @param string $attributeCode
     * @return false|int|mixed
     */
    public function getOptionId(string $label = '', string $attributeCode = '')
    {
        try {
            if (!empty($label)) {
                $brand = str_replace(' ', '', $label);
                $optionId = 0;
                if (!array_key_exists($brand, $this->arrayOption)) {
                    $optionId = $this->attributeHelper->getOptionId($attributeCode, $label);
                    if (!$optionId) {
                        $optionId = $this->attributeHelper->createOrGetId($attributeCode, $label);
                    }
                    $this->arrayOption[$brand] = $optionId;
                } else {
                    $optionId = $this->arrayOption[$brand];
                }
                return $optionId;
            }
        } catch (LocalizedException $e) {
            $this->logger->error($e->getMessage());
        }
        return false;
    }

    /**
     * Returns path file.
     *
     * @param $jsonData
     * @param $typeUri
     * @return false|string
     */
    protected function getJsonPath($jsonData, $typeUri)
    {
        $fileDir = $this->fileSystem->getDirectoryRead(DirectoryList::VAR_DIR)->getAbsolutePath();
        if (!$this->driver->isExists($fileDir . 'Aventi')) {
            $this->driver->createDirectory($fileDir . 'Aventi');
        }
        $fileJson = $fileDir . 'Aventi/' . sprintf('sap_%s_%s.json', $typeUri, date('YmdHis'));
        try {
            if (!$this->driver->isExists($fileJson)) {
                $this->driver->filePutContents($fileJson, $jsonData);
                return $fileJson;
            }
        } catch (FileSystemException $e) {
            $this->logger->error($e->getMessage());
        }
        return false;
    }

}
