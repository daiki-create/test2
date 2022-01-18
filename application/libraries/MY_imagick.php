<?php

/**
 * 画像処理ライブラリ
 *
 * @author  <y-hatano@chuco.co.jp>
 */
class MY_imagick {

    private $_imagick;
    private $_image_file = NULL;

    /**
     * Constructor
     *
     * @param   mixed properties
     */
    public function __construct($config=array())
    {
        log_debug('MY_imagick Start ----------------------------------');
        $this->initialize($config);
    }

    // --------------------------------------------------------------------

    public function initialize($config=array())
    {
        if ($this->_imagick)
        {
            log_debug("_imagick destroy.");
            $this->_imagick->destroy();
        }

        $this->_imagick = new Imagick();

        return $this;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function strip($src_path)
    {
        log_debug("MY_imagick.strip({$src_path}) run.");

        if ($this->_imagick->readimage($src_path))
        {
            $image_property = $this->_imagick->getImageProperties('exif:Orientation');
            log_debug($image_property);

            if ( ! empty($image_property['exif:Orientation']))
            {
                $degrees = 0;

                switch ($image_property['exif:Orientation'])
                {
                    case '2':
                        $this->_imagick->flopImage();
                        break;
                    case '3':
                        $this->_imagick->flopImage();
                        $this->_imagick->flipImage();
                        break;
                    case '4':
                        $this->_imagick->flipImage();
                        break;
                    case '5':
                        $degrees = 270;
                        $this->_imagick->flopImage();
                        break;
                    case '6':
                        $degrees = 90;
                        break;
                    case '7':
                        $degrees = 90;
                        $this->_imagick->flopImage();
                        break;
                    case '8':
                        $degrees = 270;
                        break;
                    default:
                        break;
                }

                if ($degrees > 0)
                {
                    $this->_imagick->rotateImage(new ImagickPixel('rgba( 0, 0, 0, 0.0)'), $degrees);
                }
            }

            if ($this->_imagick->stripImage() && $this->_imagick->writeImage($src_path))
            {
                $this->_imagick->clear();
                return TRUE;
            }
        }

        $this->_imagick->clear();
        return FALSE;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function convert2jpeg($src_path, $dst_path, $size=NULL)
    {
        log_debug("MY_imagick.convert2jpeg({$src_path}, $dst_path) run.");
        $this->_imagick->setResolution(240, 240);

        try
        {
            if ($this->_imagick->readimage($src_path))
            {
                $this->_imagick->profileImage('icc', NULL);
                $this->_imagick->setimageformat('jpg');
                $this->_imagick->setImageCompressionQuality(80);

                if ( ! empty($size) && is_array($size) && count($size) == 2)
                {
                    $this->_imagick->thumbnailimage($size[0], $size[1]);
                }

                if ($this->_imagick->writeImage($dst_path))
                {
                    $this->_imagick->clear();
                    $this->_image_file = $dst_path;
                    return $dst_path;
                }
            }
        }
        catch (Exception $e)
        {
            log_error($e->getMessage());
        }

        $this->_imagick->clear();
        return FALSE;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function convert2png($src_path, $dst_path, $size=NULL)
    {
        log_debug("MY_imagick.convert2png({$src_path}, $dst_path) run.");

        try
        {
            if ($this->_imagick->readimage($src_path))
            {
                $this->_imagick->setimageformat('png');

                if ( ! empty($size) && is_array($size) && count($size) == 2)
                {
                    $this->_imagick->thumbnailimage($size[0], $size[1]);
                }

                if ($this->_imagick->writeImage($dst_path))
                {
                    $this->_imagick->clear();
                    $this->_image_file = $dst_path;
                    return $dst_path;
                }
            }
        }
        catch (Exception $e)
        {
            log_error($e->getMessage());
        }

        $this->_imagick->clear();
        return FALSE;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function convert2tiff($src_path, $dst_path)
    {
        log_debug("MY_imagick.convert2tiff({$src_path}, {$dst_path})");
        try
        {
            if ($this->_imagick->readimage($src_path))
            {
                $this->_imagick->setimageformat('tiff');

                if ($this->colorspace() != 'CMYK')
                {
                    $this->_imagick->profileImage('icc', @file_get_contents(APPPATH.'../data/icc/sRGB_v4_ICC_preference.icc'));
                    $this->_imagick->profileImage('icc', @file_get_contents(APPPATH.'../data/icc/JapanWebCoated.icc'));

                    //色空間を変換
                    $this->_imagick->setImageColorspace(Imagick::COLORSPACE_CMYK);
                }

                if ($this->_imagick->writeImage($dst_path))
                {
                    $this->_imagick->clear();
                    $this->_image_file = $dst_path;
                    return $dst_path;
                }
            }
        }
        catch (Exception $e)
        {
            log_error($e->getMessage());
        }

        return FALSE;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function get_width_height($img_path)
    {
        if (file_exists($img_path))
        {
            try
            {
                $this->_imagick->readimage($img_path);
                $ret = array(
                    'width' => $this->_imagick->getImageWidth(),
                    'height'=> $this->_imagick->getImageHeight(),
                    'size'  => NULL,
                );
                if ($ret['width'] == 595 && $ret['height'] == 842)
                    $ret['size'] = 'a4';
                elseif ($ret['width'] == 729 && $ret['height'] == 1032)
                    $ret['size'] = 'b4';
                elseif ($ret['width'] == 842 && $ret['height'] == 1191)
                    $ret['size'] = 'a3';

                $this->_imagick->clear();
                return $ret;
            }
            catch (Exception $e)
            {
                log_error($e->getMessage());
            }
        }

        $this->_imagick->clear();
        return FALSE;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function resize($img_path, $option)
    {
        log_debug("MY_imagick.resize({$img_path}) run.");
        log_debug($option);

        if (file_exists($img_path) OR empty($option))
        {
            try
            {
                $this->_imagick->readimage($img_path);
                $width  = $this->_imagick->getImageWidth();
                $height = $this->_imagick->getImageHeight();

                log_debug("image width: {$width}");
                log_debug("image height: {$height}");

                $res = TRUE;

                if (isset($option['max_width']) && $width >= $height && $width > $option['max_width'])
                {
                    $this->_imagick->resizeImage($option['max_width'], 0, \Imagick::FILTER_LANCZOS, 1);
                    $res = $this->_imagick->writeImage();
                }
                elseif (isset($option['max_height']) && $height >= $width && $height > $option['max_height'])
                {
                    $this->_imagick->resizeImage(0, $option['max_height'], \Imagick::FILTER_LANCZOS, 1);
                    $res = $this->_imagick->writeImage();
                }

                $this->_image_file = $img_path;
                $width  = $this->_imagick->getImageWidth();
                $height = $this->_imagick->getImageHeight();
                $this->_imagick->clear();

                log_debug("image width: {$width}");
                log_debug("image height: {$height}");
                return $res;
            }
            catch (Exception $e)
            {
                log_error($e->getMessage());
            }
        }

        return FALSE;
    }
    // -----------------------------------------------------------------------------------------------------------------

    public function thumbnail($img_path, $option)
    {
        log_debug("MY_imagick.thumbnail({$img_path}) run.");
        log_debug($option);

        try
        {
            $this->_imagick->readimage($img_path);
        }
        catch (Exception $e)
        {
            log_error($e->getMessage());
            return FALSE;
        }

        $width  = $this->_imagick->getImageWidth();
        $height = $this->_imagick->getImageHeight();

        $CI = get_instance();
        $CI->load->library('image_lib');
        $CI->image_lib->clear();
        $config = array(
            'image_library' => 'gd2',
            'source_image'  => $img_path,
            'maintain_ratio'=> TRUE,
        );

        $thumb_path = preg_replace('|(.*)\.([a-z]+)$|', "$1_thumb.$2", $img_path);

        if (isset($option['thumb_height']) && $height >= $width)
        {
            if ($height > $option['thumb_height'])
            {
                $config['create_thumb'] = TRUE;
                $config['height']       = $option['thumb_height'];
                $CI->image_lib->initialize($config);

                if ( ! $CI->image_lib->resize())
                {
                    log_error('Failed to create thumbnail !');
                    return FALSE;
                }
            }
            else
            {
                if ( ! @copy($img_path, $thumb_path))
                {
                    log_error('Failed to create thumbnail !');
                    return FALSE;
                }
            }
        }
        elseif (isset($option['thumb_width']) && $width >= $height)
        {
            if ($width > $option['thumb_width'])
            {
                $config['create_thumb'] = TRUE;
                $config['width']       = $option['thumb_width'];
                $CI->image_lib->initialize($config);

                if ( ! $CI->image_lib->resize())
                {
                    log_error('Failed to create thumbnail !');
                    return FALSE;
                }
            }
            else
            {
                if ( ! @copy($img_path, $thumb_path))
                {
                    log_error('Failed to create thumbnail !');
                    return FALSE;
                }
            }
        }

        $this->_image_file = $img_path;
        return $thumb_path;
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function thumbnail_pdf($pdf_path, $jpg_path)
    {
        log_debug("MY_pdf.thumbnail_pdf({$pdf_path}, {$jpg_path}) run.");

        try
        {
            $this->_imagick->setResolution(144, 144);
            $this->_imagick->readimage($pdf_path);
            $this->_imagick->setImageFormat('jpg');
            $this->_imagick->setCompressionQuality(80);

            if ( ! $this->_imagick->writeImage($jpg_path))
            {
                $this->_imagick->clear();
                log_error('Failed to convert pdf to jpeg !');
                return FALSE;
            }

            $this->_imagick->clear();
        }
        catch (Exception $e)
        {
            log_error($e->getMessage());
            return FALSE;
        }

        $CI = get_instance();
        $CI->load->library('image_lib');
        $CI->image_lib->clear();
        $CI->image_lib->initialize(array(
            'image_library' => 'gd2',
            'source_image'  => $jpg_path,
            'create_thumb'  => FALSE,
            'maintain_ratio'=> TRUE,
            'height'         => 192,
        ));

        if ( ! $CI->image_lib->resize())
        {
            log_error('Failed to resize jpeg !');
            return FALSE;
        }

        return TRUE;
    }
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * CMYKに変換
     */
    public function convert_2_cmyk($tiff_image=NULL)
    {
        log_debug("MY_imagick.convert_2_cmyk({$tiff_image}) run.");
        $res = TRUE;
        $this->_imagick->readimage($tiff_image);
        log_debug("== ColorSpace =========================");
        log_debug($this->colorspace());

        $profiles = $this->_imagick->getImageProfiles('*', false);
        log_debug($profiles);

        //$properties = $this->_imagick->getImageProperties('icc:model');
        $properties = $this->_imagick->getImageProperties();
        log_debug($properties);

        $this->_imagick->setImageFormat('tiff');

        if (isset($properties['icc:model']) &&
            ($properties['icc:model'] == 'Japan Color 2001 Coated' OR preg_match('/^SWOP/', $properties['icc:model'])))
        {
            $this->_imagick->profileImage('icc', NULL);
        }

        if ($this->colorspace() === 'SRGB' OR $this->colorspace() === 'RGB' OR
            (isset($properties['photoshop:ICCProfile']) && $properties['photoshop:ICCProfile'] == 'Japan Color 2001 Coated') OR
            empty($profiles))
        {
            log_debug("Set Icc Profile.");
            $this->_imagick->profileImage('icc', @file_get_contents(APPPATH.'../data/icc/sRGB_v4_ICC_preference.icc'));
            $this->_imagick->profileImage('icc', @file_get_contents(APPPATH.'../data/icc/JapanWebCoated.icc'));
        }

        if ($this->colorspace() != 'CMYK')
        {
            log_debug("Convert to CMYK.");
            $this->_imagick->setImageColorspace(\Imagick::COLORSPACE_CMYK);
        }

        $properties = $this->_imagick->getImageProperties();
        log_debug($properties);

        return $this->_imagick->writeImage();
    }

    // -----------------------------------------------------------------------------------------------------------------

    public function colorspace($tiff_image=NULL)
    {
        log_debug("MY_imagick.colorspace({$tiff_image}) run.");
        if ($tiff_image)
        {
            $this->_imagick->readimage($tiff_image);
        }

        if ($colorspace = $this->_imagick->getImageColorspace())
        {
            log_debug($colorspace);
            switch ($colorspace)
            {
               case \Imagick::COLORSPACE_CMYK: // 12
                   return 'CMYK';
               case \Imagick::COLORSPACE_SRGB: // 13
                   return 'SRGB';
               case \Imagick::COLORSPACE_UNDEFINED: // 0
                   return 'UNDEFINED';
               case \Imagick::COLORSPACE_RGB: // 1
                   return 'RGB';
               case \Imagick::COLORSPACE_GRAY:
                   return 'GRAY';
               case \Imagick::COLORSPACE_TRANSPARENT:
                   return 'TRANSPARENT';
               case \Imagick::COLORSPACE_OHTA:
                   return 'OHTA';
               case \Imagick::COLORSPACE_LAB:
                   return 'LAB';
               case \Imagick::COLORSPACE_XYZ:
                   return 'XYZ';
               case \Imagick::COLORSPACE_YCBCR:
                   return 'YCBCR';
               case \Imagick::COLORSPACE_YCC:
                   return 'YCC';
               case \Imagick::COLORSPACE_YIQ:
                   return 'YIQ';
               case \Imagick::COLORSPACE_YPBPR:
                   return 'YPBPR';
               case \Imagick::COLORSPACE_YUV:
                   return 'YUV';
               case \Imagick::COLORSPACE_HSB:
                   return 'HSB';
               case \Imagick::COLORSPACE_HSL:
                   return 'HSL';
               case \Imagick::COLORSPACE_HWB:
                   return 'HWB';
               case \Imagick::COLORSPACE_REC601LUMA:
                   return 'REC601LUMA';
               case \Imagick::COLORSPACE_REC709LUMA:
                   return 'REC709LUMA';
               case \Imagick::COLORSPACE_LOG:
                   return 'LOG';
               case \Imagick::COLORSPACE_CMY:
                   return 'CMY';
            }
        }

        if ($tiff_image)
        {
            $this->_imagick->clear();
        }
        return NULL;
    }

    // -----------------------------------------------------------------------------------------------------------------

    // ================================================================================================================

    public function __call($method_name, $arguments=array())
    {
        if (method_exists($this->_imagick, $method_name))
        {
            return call_user_func_array([$this->_imagick, $method_name], $arguments);
        }

        log_error("Not implemented method. [{$method_name}]");
    }

}

