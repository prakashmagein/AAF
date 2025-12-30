<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Custom Reports Builder for Magento 2
 */

namespace Amasty\ReportBuilder\Console\Command;

use Amasty\ReportBuilder\Model\Indexer\Stock\Indexer as StockIndexer;
use Amasty\ReportBuilder\Model\Indexer\Stock\IndexerPool;
use Exception;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReindexStockCommand extends Command
{
    /**
     * @var StockIndexer
     */
    private $stockIndexer;

    /**
     * @var IndexerPool
     */
    private $indexerPool;

    public function __construct(StockIndexer $stockIndexer)
    {
        $this->stockIndexer = $stockIndexer;
        $this->indexerPool = ObjectManager::getInstance()->get(IndexerPool::class);
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('ambuilder:stock:update')
            ->setDescription('Reindexes Stock Data');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $returnValue = Cli::RETURN_SUCCESS;
        try {
            $output->write('Stock Data ');

            $startTime = microtime(true);

            $this->action();

            $resultTime = microtime(true) - $startTime;

            $output->writeln(
                __('has been rebuilt successfully in %time', ['time' => gmdate('H:i:s', (int) $resultTime)])->render()
            );
        } catch (LocalizedException $e) {
            $output->writeln(__('exception: %message', ['message' => $e->getMessage()])->render());
            $returnValue = Cli::RETURN_FAILURE;
        } catch (Exception $e) {
            $output->writeln('process unknown error:');
            $output->writeln($e->getMessage());

            $output->writeln($e->getTraceAsString(), OutputInterface::VERBOSITY_DEBUG);
            $returnValue = Cli::RETURN_FAILURE;
        }

        return $returnValue;
    }

    private function action(): void
    {
        $this->indexerPool->execute();
    }
}
