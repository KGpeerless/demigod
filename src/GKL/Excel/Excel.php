<?php
namespace GKL\Excel;


class Excel implements ExcelInterface
{
    /**
     * @var array 传入数据规范
     */
    private static $exportOptions = [
        // 导出字段
        'fields' => [],
        // 导出数据
        'data'   => [],
        //
        'style'  => [
        ],
        // 额外信息
        'extra'  => [
            'title' => '', // excel 标题
        ]
    ];

    private static $defaultOptions = [
        'title'      => '', // excel 标题
        'subTitle'   => '',
        'fontFamily' => '楷体',
        'height'     => 30,
        'width'      => 30,     // 单元格宽度
        'style'      => [
            'title'   => [
                'font'      => [
                    // excel 要求格式
                    'font'      => ['bold'  => true,
                                    'size'  => 16,
                                    'color' =>
                                        ['argb' => 'FF25281B']],
                    'alignment' => ['horizontal' => 'center',
                                    'vertical'   => 'center']
                ],
                'rowHeight' => '30'
            ],
            'fields'  => [
                'font'      => [
                    // excel 要求格式
                    'font'      => ['bold'  => true,
                                    'size'  => 13,
                                    'color' =>
                                        ['argb' => '00000000']],
                    'alignment' => ['horizontal' => 'center',
                                    'vertical'   => 'center']
                ],
                'rowHeight' => '35'
            ],
            'content' => [
                'font' => [
                    // excel 要求格式
                    'font'      => [
                        'bold'  => false,
                        'size'  => 12,
                        'color' => [
                            'argb' => '00000000',
                        ]
                    ],
                    'alignment' => [
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ]
                ],

                'rowHeight' => '30',   // 行高
            ],
            'borders' => [
                'outline' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                    //'style' => PHPExcel_Style_Border::BORDER_THICK, 另一种样式
                    'color' => ['argb' => 'FF000000'],     //设置border颜色
                ],
            ],
            'link'    => [      // 包含链接单元格的宽度
                'width' => 50
            ],
            'image'   => [  // 单元格图片设置
                'height'    => 100,
                'width'     => 50,
                'alignment' => [
                    'alignment' => [
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ]
                ]
            ]
        ],

        'creator'     => '', // 创建人
        'lastModify'  => '', // 最后修改人.
        'subject'     => '', // 题目
        'description' => '', // 描述
        'keywords'    => '', //关键词
        'category'    => '', // 种类
    ];

    // 导入excel表格
    public function importAction($originFile)
    {
        // TODO: Implement importAction() method.
    }

    /**
     * 导出excel
     *
     * @param array   $exportOptions 导出数据设置
     *
     * @param string  $filename      导出文件名
     * @param string  $targetDir     导出目录(默认直接下载)
     * @param boolean $debug         是否调试
     */
    public static function exportAction(array $exportOptions, $filename = '', $targetDir = '', $debug = false)
    {
        $exportOptions = array_merge(self::$exportOptions, $exportOptions);
        $objPHPExcel   = new \PHPExcel();

        $fields      = $exportOptions['fields'];
        $data        = $exportOptions['data'];
        $originIndex = 65;
        $chrIndex    = $originIndex; // 列从A开始计算
        $rowIndex    = 1;
        $chrfool     = 1;

        // 设置标题
        if ($title = $exportOptions['extra']['title']) {
            $rowEnd = $chrIndex + count($fields) - 1;
            $cell   = 'A' . $rowIndex;
            $objPHPExcel
                ->getActiveSheet()
                ->mergeCells($cell . ':' . chr((string)$rowEnd) . $rowIndex)// 合并单元格
                ->setCellValue($cell, $title, true)
                ->getStyle($cell)
                ->applyFromArray(self::$defaultOptions['style']['title']['font']);

            $objPHPExcel
                ->getActiveSheet()
                ->getRowDimension($rowIndex)
                ->setRowHeight(self::$defaultOptions['style']['title']['rowHeight']);

            $rowIndex += 1;
        }

        // 设置字段高度
        $objPHPExcel
            ->getActiveSheet()
            ->getRowDimension($rowIndex)
            ->setRowHeight(self::$defaultOptions['style']['fields']['rowHeight']);

        // 设置字段
        foreach ($fields as $k => $v) {
            $char = self::generateCellName($chrIndex, $chrfool);
            $cell = $char . $rowIndex;

            $objPHPExcel->getActiveSheet()
                ->setCellValue($cell, $v);

            $objPHPExcel->getActiveSheet()
                ->getStyle($cell)
                ->applyFromArray(self::$defaultOptions['style']['fields']['font']);

            // 设置每列的宽度
            $objPHPExcel->getActiveSheet()
                ->getColumnDimension($char)
                ->setWidth(self::$defaultOptions['width']);
        }
        $chrIndex = $originIndex;    // 列从A开始计算
        $rowIndex += 1;

        // 设置内容
        $fields = array_keys($fields);
        foreach ($data as $item) {
            $chrfool = 1;

            foreach ($fields as $f) {
                $char = self::generateCellName($chrIndex, $chrfool);
                $cell = $char . $rowIndex;

                $objPHPExcel
                    ->getActiveSheet()
                    ->getRowDimension($rowIndex)
                    ->setRowHeight(self::$defaultOptions['style']['content']['rowHeight']);
                self::setCellValues($objPHPExcel, $cell, isset($item[$f]) ? $item[$f] : '');
            }

            $rowIndex++;
            $chrIndex = $originIndex;
        }

        // 文档属性
        $objPHPExcel
            ->getProperties()
            ->setCreator(self::$defaultOptions['creator'])
            ->setLastModifiedBy(self::$defaultOptions['lastModify'])
            ->setTitle(self::$defaultOptions['title'])
            ->setSubject(self::$defaultOptions['subject'])
            ->setDescription(self::$defaultOptions['description'])
            ->setKeywords(self::$defaultOptions['keywords'])
            ->setCategory(self::$defaultOptions['category']);

        // 默认属性
        $objPHPExcel
            ->getDefaultStyle()
            ->getFont()
            ->setName(iconv('gbk', 'utf-8', self::$defaultOptions['fontFamily']))
            ->setSize();

        self::outPutAction($objPHPExcel, $filename, $targetDir, 'excel', $debug);
    }

    /**
     * 导出表格
     *
     * @param \PHPExcel $PHPExcel
     * @param string    $filename  导出文件名
     * @param string    $targetDir 导出文件目录
     * @param string    $type      导出类型 excel|pdf
     * @param bool      $debug     是否直接显示在浏览器上
     */
    private static function outPutAction(\PHPExcel $PHPExcel, $filename, $targetDir = '', $type = 'excel', $debug = false)
    {
        if ($debug) {
            $objHtmlWriter = new \PHPExcel_Writer_HTML($PHPExcel);
            $objHtmlWriter->save("php://output");

            return;
        }

        switch (true) {
            case $type == 'excel':
                $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
                if (!$targetDir) {
                    header("Content-Type: application/vnd.ms-excel;");
                    $filename = $filename ?: 'abc' . '.xls';
                    header("Content-Disposition:attachment;filename=" . $filename);
                }
                break;
            case $type == 'pdf':
                $objWriter = \PHPExcel_IOFactory::createWriter($PHPExcel, 'PDF');
                if (!$targetDir) {
                    $filename = $filename ?: 'abc' . '.pdf';
                    header("Content-Disposition:attachment;filename=" . $filename);
                    $objWriter->setSheetIndex(0);
                    header("Content-Type: application/pdf");
                }
                break;
        }

        if ($targetDir) {
            $objWriter->save($targetDir . '/' . $filename);
        } else {
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");
            header("Content-Transfer-Encoding:binary");

            $objWriter->save("php://output");
        }
    }

    /**
     * 设置单元格数据
     *
     * @param \PHPExcel $PHPExcel
     * @param string    $cell 单元格
     * @param string    $val  单元格值
     */
    private static function setCellValues(\PHPExcel $PHPExcel, $cell, $val)
    {
        if ($val) {
            switch (true) {
                // 布尔类型
                case is_bool($val):
                    self::writeBoolValue($PHPExcel, $cell, $val);
                    break;

                // 对象格式
                case is_object($val):
                    self::writeObjectValue($PHPExcel, $cell, $val);
                    break;

                // 连接格式
                case is_string($val) && preg_match('/^(http|https)(.*?)\.com|cn|cc$/i', $val):
                    self::writeLinkValue($PHPExcel, $cell, $val);
                    break;

                // 图片格式
                case is_string($val) && preg_match('/^.*?\.jpeg|jpg|png|gif$/i', $val) && file_exists($val):
                    self::writeImageValue($PHPExcel, $cell, $val);
                    break;

                // 纯文本格式
                default:
                    self::writeTextValue($PHPExcel, $cell, $val);
            }
        }

        // 内容垂直居中
        $PHPExcel
            ->getActiveSheet()
            ->getStyle($cell)
            ->getAlignment()
            ->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
    }

    /**
     * 设置图片格式数据
     *
     * @param \PHPExcel $objPHPExcel
     * @param string    $cell      单元格
     * @param string    $imagePath 图片地址
     * @param string    $imageName 图片名称
     * @param string    $imageDesc 图片描述
     */
    private static function writeImageValue(\PHPExcel $objPHPExcel, $cell, $imagePath, $imageName = '', $imageDesc = '')
    {
        $objDrawing = new \PHPExcel_Worksheet_Drawing();
        $objDrawing
            ->setWorksheet($objPHPExcel->getActiveSheet())
            ->setName($imageName)
            ->setDescription($imageDesc)
            ->setCoordinates($cell);

        $objDrawing
            ->setHeight(self::$defaultOptions['style']['image']['height'] / 100)// 设置为下面的0.01倍 不知道原因
            ->setWidth(self::$defaultOptions['style']['image']['width']);

        $objDrawing->setPath(substr($imagePath, 0, 1) == '/' ? substr($imagePath, 1) : $imagePath, true);
    }

    /**
     * 设置文本格式数据
     *
     * @param \PHPExcel $objPHPExcel
     * @param string    $cell 单元格
     */
    private static function writeTextValue(\PHPExcel $objPHPExcel, $cell, $val)
    {
        $objPHPExcel
            ->getActiveSheet()
            ->setCellValue($cell, $val)
            ->getStyle($cell)
            ->applyFromArray(self::$defaultOptions['style']['content']['font']);
    }

    /**
     * 设置对象格式数据
     *
     * @param \PHPExcel $objPHPExcel
     * @param string    $cell 单元格
     */
    private static function writeObjectValue(\PHPExcel $objPHPExcel, $cell, $val)
    {
        $objPHPExcel
            ->getActiveSheet()
            ->setCellValue($cell, $val)
            ->getStyle($cell)
            ->applyFromArray(self::$defaultOptions['style']['content']['font']);
    }

    /**
     * 设置布尔格式数据
     *
     * @param \PHPExcel $objPHPExcel
     * @param string    $cell 单元格
     * @param string    $val
     */
    private static function writeBoolValue(\PHPExcel $objPHPExcel, $cell, $val)
    {
        $objPHPExcel
            ->getActiveSheet()
            ->setCellValue($cell, $val ? '是' : '否')
            ->getStyle($cell)
            ->applyFromArray(self::$defaultOptions['style']['content']['font']);
    }

    /**
     * 设置连接格式数据
     *
     * @param \PHPExcel $objPHPExcel
     * @param string    $cell 单元格
     * @param string    $val
     */
    private static function writeLinkValue(\PHPExcel $objPHPExcel, $cell, $val)
    {
        // 设置该列的宽度
        $objPHPExcel->getActiveSheet()
            ->getColumnDimension(substr($cell, 0, 1))
            ->setWidth(self::$defaultOptions['style']['link']['width']);

        $objPHPExcel->getActiveSheet()
            ->setCellValue($cell, iconv('gbk', 'utf-8', $val))
            ->getCell($cell)
            ->getHyperlink()
            ->setUrl($val);

        // 设置单元格内容格式
        $objPHPExcel
            ->getActiveSheet()
            ->getStyle($cell)
            ->applyFromArray(self::$defaultOptions['style']['content']['font']);
    }

    /**
     * 生成列名
     *
     * @param int $chrIndex
     * @param int $chrfool
     *
     * @return string
     */
    private static function generateCellName(&$chrIndex = 65, &$chrfool = 1)
    {
        if ($chrIndex == 91) {
            $chrIndex = 65;
            $chrfool++;
        }
        $char = chr((string)$chrIndex++);
        for ($i = 1; $i < $chrfool; $i++) {
            if ($i >= 2) {
                break;
            }
            $char = (chr((string)(65 + $chrfool - 2))) . "" . $char;
        }
        return $char;
    }
}
