<?php

/**
 * PHP Version 7.2
 * Command to generate CSV
 *
 * @category Class
 *
 * @package Command
 *
 * @author AshishS <sarodeashish81@gmail.com>
 *
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Services\CsvManager;

/**
 * Class for CreateCsvCommand
 *
 * @category Class
 *
 * @package Command
 *
 * @author AshishS <sarodeashish81@gmail.com>
 *
 */
class CreateCsvCommand extends Command {

    protected static $defaultName = 'app:create-calendar-csv';
    var $csvManager;

    const EXTENSIONS = ["csv"];

    protected function configure() {
        $this->setName('app:create-calendar-csv')
                ->setDescription('Calendar date csv.')
                ->addArgument('filename', InputArgument::REQUIRED, 'Name of the output file')
                ->addArgument('year', InputArgument::OPTIONAL, 'yyyy');
    }

    public function __construct(CsvManager $csvManager) {
        parent::__construct();
        $this->csvManager = $csvManager;
    }

    /**
     * Function to execute command operations
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        try {

            $ext = pathinfo($input->getArgument('filename'), PATHINFO_EXTENSION);
            $fileName = (!empty($ext)) ? $input->getArgument('filename') : $input->getArgument('filename') . 'csv';

            $year = (empty($input->getArgument('year'))) ? date('Y', time()) : $input->getArgument('year');

            /* Validating file extensions */
            if (!empty($ext) && !in_array($ext, self::EXTENSIONS)) {
                throw new \Exception("Failed : Invalid file name.");
            }

            /* Call CsvManager Service method */
            if ($this->csvManager->exportCalendar(['fileName' => $fileName, 'year' => $year])) {
                $output->writeln("Success: $year-$fileName is generated ");
            } else {
                throw new \Exception("Failed : Something went wrong. Please try again.");
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

}

?>