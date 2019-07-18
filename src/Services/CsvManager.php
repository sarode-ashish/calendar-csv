<?php

/**
 * PHP Version 7.2
 * Service to generate CSV
 *
 * @category Class
 *
 * @package Services
 *
 * @author AshishS <sarodeashish81@gmail.com>
 *
 */

namespace App\Services;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class for CsvManager
 *
 * @category Class
 *
 * @package Services
 *
 * @author AshishS <sarodeashish81@gmail.com>
 *
 */
class CsvManager {

    var $params;
    var $rootDir;

    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
        $this->rootDir = $this->params->get('kernel.project_dir') . DIRECTORY_SEPARATOR . 'public';
    }

    /**
     * Function to export calendar dates to CSV
     * 
     * @param Array $params
     * @return boolean
     * @throws \Exception
     */
    public function exportCalendar($params = []) {
        if (empty($params)) {
            return false;
        }
        $params['year'] = (!isset($params['year']) || empty($params['year'])) ? date('Y', time()) : $params['year'];
        $filePath = $this->rootDir . DIRECTORY_SEPARATOR . $params['year'] . '-' . $params['fileName'];

        try {
            $header = ["Month", "Bonus Payment Date", "Salary Payment Date"];
            $list = [];
            $file = fopen($filePath, "w");
            fputcsv(
                    $file, $header
            );
            for ($month = 1; $month <= 12; $month++) {
                $list[] = $this->getBonusPaymentDate($month, $params['year']);
            }
            foreach ($list as $line) {
                fputcsv(
                        $file, $line, ','
                );
            }
            fclose($file);
            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Function to get Bonus Payment date (15th of the month or/ next wednesday )
     * 
     * @param int $month
     * @param int $year
     * @return array
     */
    public function getBonusPaymentDate($month, $year) {

        $monthEndDate = date('t', strtotime($year . DIRECTORY_SEPARATOR . $month . DIRECTORY_SEPARATOR . "01"));
        $arr = [];
        for ($d = 1; $d <= $monthEndDate; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            $arr[0] = date('F', $time);
            if (date('d', $time) == 15) {
                if (!in_array(date('l', $time), ['Saturday', 'Sunday'])) {
                    $arr[2] = date('Y-m-d', $time);
                } else {
                    $arr[2] = date('Y-m-d', strtotime('next Wednesday', $time));
                }
            } elseif (date('d', $time) == $monthEndDate) {
                if (!in_array(date('l', $time), ['Saturday', 'Sunday'])) {
                    $arr[1] = date('Y-m-d', $time);
                } else {
                    $arr[1] = date('Y-m-d', strtotime('previous Friday', $time));
                }
            }
        }
        return $arr;
    }

}
