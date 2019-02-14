<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

include_once APP_ROOT . '/app/Libraries/PHPExcel/Classes/PHPExcel.php';

class ExcelProcessor
{

    /**
     * PHPExcel 实例
     * @var
     */
    private $phpExcelObj;

    /**
     * PHPExcel writer
     * @var
     */
    private $writer;

    /**
     * 一个表格
     * @var
     */
    private $sheet;

    /**
     * 表格头
     * @var
     */
    private $header;

    /**
     * 导出文档的保存根路径
     * @var
     */
    const SAVE_PATH = 'export_root';

    /**
     * 错误描述
     * @var
     */
    private $error;

    /**
     * ExcelProcessor constructor.
     */
    public function __construct()
    {
        $this->phpExcelObj = new \PHPExcel();
        $this->sheet = $this->phpExcelObj->setActiveSheetIndex(0);
        $this->writer = \PHPExcel_IOFactory::createWriter($this->phpExcelObj, 'Excel2007');
    }

    /**
     * 设置表格头
     * @param array $header
     * @return $this
     */
    public function setHeader($header = [])
    {
        if (!empty($header) && is_array($header)) {
            $this->header = $header;
            $cord = 'A';
            $num = 1;
            foreach ($this->header as $head) {
                $this->sheet->setCellValue($cord . $num, $head);
                $cord++;
            }
        }
        return $this;
    }

    /**
     * 设置表格标题
     * @param $title
     * @return $this
     */
    public function setSheetTitle($title)
    {
        $this->sheet->setTitle($title);
        return $this;
    }

    /**
     * 设置表格体数据
     * @param array $data
     * @return $this
     */
    public function setData($data = [])
    {
        $i = 2;
        foreach ($data as $k => $v) {
            if (!is_array($v)) {
                $v = (array)$v;
            }
            $cord = 'A';
            foreach ($v as $sk => $sv) {
                $this->sheet->setCellValue($cord . $i, $sv . ' ');
                $cord++;
            }
            $i++;
        }
        return $this;
    }

    /**
     * 保存到导出路径
     * @param string $filename
     */
    public function save($filename = '')
    {
        if (!$filename) {
            $filename = str_random() . '.xlsx';
        }
        if (!file_exists(self::SAVE_PATH)) {
            mkdir(self::SAVE_PATH);
        }

        // 导出文档的用户路径
        $userPath = self::SAVE_PATH . '/' . session()->get('id');
        if (!file_exists($userPath)) {
            mkdir($userPath);
        }
        $documentPath = $userPath . '/' . date('Ymd');
        if (!file_exists($documentPath)) {
            mkdir($documentPath);
        }
        // 文件的系统相对路径
        $relativePath = $documentPath . '/' . $filename;
        // 文件的系统绝对路径
        $absolutePath = APP_ROOT . '/' . $documentPath . '/' . $filename;

        // 数据存表，如果是直接下载的，则不用进行存表操作
        $record = [
            'userid' => session()->get('id'),
            'name' => $filename,
            'path' => $relativePath,
            'create_date' => date('Y-m-d H:i:s')
        ];
        DB::table('bus_export_files')->insert($record);

        $this->writer->save($absolutePath);
        exit;
    }

    /**
     * 下载表格
     * @param string $filename
     */
    public function download($filename = '')
    {
        if (!$filename) {
            $filename = str_random() . '.xlsx';
        }
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $this->writer->save("php://output");
        exit;
    }

    /**
     * 读取一个Excel文档内容
     * @param $file
     * @return mixed
     */
    public function read($file)
    {
        if (!file_exists($file)) {
            $this->error = '文件不存在';
            return false;
        }
        $objPHPExcel = \PHPExcel_IOFactory::load($file);
        $sheets = $objPHPExcel->getAllSheets();
        $workbook = [];
        foreach ($sheets as $sheet) {
            $workSheet = [];
            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $onerow = [];
                foreach ($cellIterator as $cell) {
                    $onerow[] = $cell->getValue();
                }
                $workSheet[] = $onerow;
            }
            $workbook[] = $workSheet;
        }
        return $workbook;
    }

}