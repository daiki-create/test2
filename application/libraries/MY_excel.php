<?php

require APPPATH . 'includes/php-spreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class MY_Excel {

    private $_book;
    private $_sheet;

    private $_properties = array(
        'creator'           => 'Unknown Creator',
        'last_modified_by'  => '',
        'company'           => 'Unknown Company',
        'created'           => '',
        'modified'          => '',
        'manager'           => '',
        'title'             => 'Untitled Spreadsheet',   // Title
        'subject'           => '',   // Sub Title
        'description'       => '',
        'keywords'          => '',
        'category'          => '',
    );

    public function __construct($config=array())
    {
        if (is_array($config))
        {
            foreach ($this->_properties as $property => $_v)
            {
                if (isset($config[$property]))
                    $this->_properties[$property] = $config[$property];
            }
        }

        log_message('DEBUG', 'MY_spreadsheet Initialized.');
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_properties($properties)
    {
        foreach ($properties as $property => $value)
        {
            $this->set_property($property, $value);
        }

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_property($property, $value='')
    {
        if (isset($this->_properties[$property]))
        {
            $this->_properties[$property] = $value;
        }

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    /**
     * Open Excel book
     */
    public function open($file_path=NULL)
    {
        try
        {
            if (empty($file_path))
            {
                $this->_book = new Spreadsheet();
                $this->_sheet = $this->_book->getActiveSheet();
                return $this;
            }
            elseif (file_exists($file_path))
            {
                $reader = new Reader();
                $this->_book = @$reader->load($file_path);
                $this->_sheet = $this->_book->getActiveSheet();
                return $this;
            }
            else
            {
                $this->_book  = NULL;
                $this->_sheet = NULL;
            }
        }
        catch(Exception  $e)
        {
            log_message('error', 'PhpSpreadsheet throw Exception.');
            log_message('error', $e->getMessage());
            $this->_book  = NULL;
            $this->_sheet = NULL;
        }

        log_message('ERROR', 'Failed to open excel book.');
        return NULL;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    /**
     * Active Sheet
     */
    public function active_sheet($index=0)
    {
        if ($index < $this->_book->getSheetCount())
        {
            $this->_sheet = $this->_book->getSheet($index);
            return TRUE;
        }

        log_message('ERROR', 'Not exists the sheet index of ['.$index.'].');
        return FALSE;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    /**
     * Add Sheet
     */
    public function add_sheet($index=NULL, $sheet_title=NULL)
    {
        if (is_null($sheet_title))
            $new_sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($this->_book);
        else
            $new_sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($this->_book, $sheet_title);

        $this->_sheet = $this->_book->addSheet($new_sheet, $index);
        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    /**
     * Sheet Title
     */
    public function sheet_title($index=NULL, $sheet_title=NULL)
    {
        if (is_null($index))
        {
            if (is_null($sheet_title))
            {
                return $this->_sheet->getTitle();
            }
            else
            {
                $this->_sheet->setTitle($sheet_title);
                return $sheet_title;
            }
        }
        else
        {
            if (is_null($sheet_title))
            {
                return $this->_book->getSheet($index)->getTitle();
            }
            else
            {
                return $this->_book->getSheet($index)->setTitle($sheet_title);
                return $sheet_title;
            }
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_value($cel, $val, $wrap_text=FALSE)
    {
        try
        {
            $this->_sheet->setCellValue($cel, $val);

            if ($wrap_text === TRUE)
            {
                $this->_sheet->getStyle($cel)->getAlignment()->setWrapText(true);
            }

            return $this;
        }
        catch (Exception $e)
        {
            log_message('error', 'PhpSpreadsheet throw Exception.');
            log_message('error', $e->getMessage());
            return FALSE;
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_datetime($cel, $datetime)
    {
        if ( ! empty($datetime))
        {
            $datetime += 32400; // +9hour
            $this->_sheet->setCellValue($cel, \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($datetime));
        }

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_font_size($cel, $size)
    {
        try
        {
            $this->_sheet->getStyle($cel)->getFont()->setSize($size);
            return $this;
        }
        catch (Exception $e)
        {
            log_message('error', 'PhpSpreadsheet throw Exception.');
            log_message('error', $e->getMessage());
            return FALSE;
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_font_style($cel, $style, $bool=TRUE)
    {
        $font = $this->_sheet->getStyle($cel)->getFont();

        switch ($style)
        {
            case 'bold':
                $font->setBold($bool);
                break;
            case 'italic':
                $font->setItalic($bool);
                break;
            case 'underline':
                $font->setUnderline($bool);
                break;
            case 'strikethrough':
                $font->setStrikethrough($bool);
                break;
        }

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_align($cel, $align)
    {
        $style = $this->_sheet->getStyle($cel)->getAlignment();

        switch ($align)
        {
            case 'left':
                $style->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                break;
            case 'right':
                $style->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                break;
            case 'center':
                $style->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                break;
        }

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_valign($cel, $valign)
    {
        $style = $this->_sheet->getStyle($cel)->getAlignment();

        switch ($valign)
        {
            case 'top':
                $style->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                break;
            case 'bottom':
                $style->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
                break;
            case 'middle':
                $style->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                break;
        }

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    /**
     * Set Font Color                不透明度
     *      black      = 'FF000000'       FF = 100%
     *      white      = 'FFFFFFFF'       E6 =  90%
     *      red        = 'FFFF0000'       CC =  80%
     *      darkred    = 'FF800000'       B3 =  70%
     *      blue       = 'FF0000FF'       99 =  60%
     *      darkblue   = 'FF000080'       80 =  50%
     *      green      = 'FF00FF00'       66 =  40%
     *      darkgreen  = 'FF008000'       4D =  30%
     *      yellow     = 'FFFFFF00'       33 =  20%
     *      darkyellow = 'FF808000'       1A =  10%
     */
    public function set_font_color($cel, $argb)
    {
        $argb = $this->_argb($argb);
        $this->_sheet->getStyle($cel)->getFont()->getColor()->setARGB($argb);
        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    /**
     * Set Background Color
     *         pattern
     *         - none
     *         - solid
     *         - linear
     *         - path
     *         - darkDown
     *         - darkGray
     *         - darkGrid
     *         - darkHorizontal
     *         - darkTrellis
     *         - darkUp
     *         - darkVertical
     *         - gray0625
     *         - gray125
     *         - lightDown
     *         - lightGray
     *         - lightGrid
     *         - lightHorizontal
     *         - lightTrellis
     *         - lightUp
     *         - lightVertical
     *         - mediumGray
     */
    public function set_bg_color($cel, $argb, $pattern='solid')
    {
        try
        {
            $argb = $this->_argb($argb);
            $fill = $this->_sheet->getStyle($cel)->getFill()->setFillType($pattern)->getStartColor()->setARGB($argb);
            return $this;
        }
        catch (Exception $e)
        {
            log_message('error', 'PhpSpreadsheet throw Exception.');
            log_message('error', $e->getMessage());
            return FALSE;
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_border($cel, $position='all', $style='', $argb=NULL)
    {
        $borders = $this->_sheet->getStyle($cel)->getBorders();

        switch ($position)
        {
            case "left":
                $border = $borders->getLeft();
                break;
            case "right":
                $border = $borders->getRight();
                break;
            case "top":
                $border = $borders->getTop();
                break;
            case "bottom":
                $border = $borders->getBottom();
                break;
            case "outline":
                $border = $borders->getOutline();
                break;
            case "inside":
                $border = $borders->getInside();
                break;
            case "vertical":
                $border = $borders->getVertical();
                break;
            case "horizontal":
                $border = $borders->getHorizontal();
                break;
            default:
                $border = $borders->getAllBorders();
                break;
        }

        switch ($style)
        {
            case "none": // 罫線なし
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE);
                break;
            case "dashed": // 点線（長め）
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHED);
                break;
            case "dotted": // 点線（短め）
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED);
                break;
            case "thick": // 太線
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
                break;
            case "double": // 二重線
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);
                break;
            case "hair": // ヘアライン
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR);
                break;
            case "mediumdashed": // 普通の点線
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHED);
                break;
            case "dashdot": // 一点鎖線
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHDOT);
                break;
            case "mediumdashdot": // 普通線の一点鎖線
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHDOT);
                break;
            case "dashdotdot": // 二点鎖線
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHDOTDOT);
                break;
            case "mediumdashdotdot": // 普通線の二点鎖線
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUMDASHDOTDOT);
                break;
            case "slantdashdot": //斜めにカットされた一点鎖線
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_SLANTDASHDOT);
                break;
            case "midium":
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                break;
            default:
                $border->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                break;
        }

        if ( ! is_null($argb))
        {
            $argb = $this->_argb($argb);
            $border->getColor()->setARGB($argb);
        }

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_format($cel, $format)
    {
        $this->_sheet->getStyle($cel)->getNumberFormat()->setFormatCode($format);

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    /**
     *
     * @param $col_name カラム名 A, B, C...
     */
    public function set_width($col_name, $width)
    {
        $this->_sheet->getColumnDimension($col_name)->setWidth($width);

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function set_height($row_num, $height)
    {
        $this->_sheet->getRowDimension($row_num)->setRowHeight($height);

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function merge_cells($cel)
    {
        $this->_sheet->mergeCells($cel);

        return $this;
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    /**
     * Save
     */
    public function save($save_path)
    {
        try
        {
            $this->_book->getProperties()
                 ->setCreator($this->_properties['creator'])
                 ->setLastModifiedBy($this->_properties['last_modified_by'])
                 ->setCompany($this->_properties['company'])
                 ->setCreated(strtotime($this->_properties['created']))
                 ->setModified(strtotime($this->_properties['modified']))
                 ->setManager($this->_properties['manager'])
                 ->setTitle($this->_properties['title'])
                 ->setSubject($this->_properties['subject'])
                 ->setDescription($this->_properties['description'])
                 ->setKeywords($this->_properties['keywords'])
                 ->setCategory($this->_properties['category']);
            $writer = new Writer($this->_book);
            $writer->save($save_path);
            return TRUE;
        }
        catch(Exception  $e)
        {
            log_message('error', 'PhpSpreadsheet throw Exception.');
            log_message('error', $e->getMessage());
            $this->_book  = NULL;
            $this->_sheet = NULL;
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function lock_cell($cel, $password)
    {
        $max_col = $this->_sheet ->getHighestColumn();
        $max_row = $this->_sheet ->getHighestRow();

        if (preg_match('|^[A-Za-z]+$|', $cel))
        {
            $cel = "{$cel}1:".$cel . $max_row;
        }
        elseif (preg_match('|^[A-Za-z]+$|', $cel))
        {
            $cel = "A{$cel}:{$max_col}{$cel}";
        }

        $this->_sheet->getStyle("A1:{$max_col}{$max_row}")->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $this->_sheet->getStyle($cel)->getProtection()->setLocked(Protection::PROTECTION_PROTECTED);
        $this->_sheet->getProtection()->setPassword($password);
        $this->_sheet->getProtection()->setSheet(true);
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function get_rows($columns)
    {
        if ( ! $this->_sheet)
            return FALSE;

        $values = [];

        $i = 0;
        foreach ($this->_sheet->getRowIterator() as $row)
        {
            $ri = $row->getRowIndex();
            $values[$i] = [];

            foreach ($columns as $col)
            {
                $values[$i][$col] = $this->_sheet->getCell("{$col}{$ri}")->getValue();
            }

            $i++;
        }

        return $values;
    }

    // ==================================================================================================================================

    private function _argb($argb)
    {
        switch ($argb)
        {
            case 'black':      $argb = 'FF000000'; break;
            case 'white':      $argb = 'FFFFFFFF'; break;
            case 'red':        $argb = 'FFFF0000'; break;
            case 'darkred':    $argb = 'FF800000'; break;
            case 'blue':       $argb = 'FF0000FF'; break;
            case 'darkblue':   $argb = 'FF000080'; break;
            case 'green':      $argb = 'FF00FF00'; break;
            case 'darkgreen':  $argb = 'FF008000'; break;
            case 'yellow':     $argb = 'FFFFFF00'; break;
            case 'darkyellow': $argb = 'FF808000'; break;
            default: $argb = strtoupper($argb);
        }

        return $argb;
    }
}

