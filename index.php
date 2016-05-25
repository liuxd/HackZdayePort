<?php

require 'vendor/autoload.php';

use Intervention\Image\ImageManager;

class Image2txt
{
    const SINGLE_WORD_WIDTH = 8;
    const WORD_COLOR_VALUE = 4278190080;

    /**
     * Get object of the image.
     * @param string $sImagePath
     */
    public function __construct($sImagePath)
    {
        $manager = new ImageManager(array('driver' => 'imagick'));
        $this->image = $manager->make($sImagePath);

        $this->width = $this->image->width();
        $this->height = $this->image->height();
    }

    private function getMatrix()
    {
        $aMatrix = [];

        foreach (range(0, $this->width - 1) as $x) {
            foreach (range(0, $this->height - 1) as $y) {
                $iColorValue = $this->image->pickColor($x, $y, 'int');
                $aMatrix[$x][$iColorValue][] = $y;
            }
        }

        return $aMatrix;
    }

    private function getTextByMatrix($aMatrix)
    {
        $this->getStandard();
        $aTarget = [];

        foreach ($aMatrix as $x => $values) {
            foreach ($values as $y) {
                $aTarget[] = $x . ',' . $y;
            }
        }

        foreach ($this->aStandard as $iNumber => $aStandardMatrix) {
            $aDiff = array_diff($aTarget, $aStandardMatrix);

            if (count($aDiff) < 2) {
                return $iNumber;
            }
        }
    }

    private function getStandard()
    {
        if (empty($this->aStandard)) {
            $this->aStandard = array_map(function($v){
                $arr = json_decode(trim($v), true);
                $output = [];

                foreach ($arr as $x => $values) {
                    foreach ($values as $y) {
                        $output[] = $x . ',' . $y;
                    }
                }

                return $output;
            }, file(__DIR__ . '/standard.json'));            
        }

        return $this->aStandard;
    }

    public function main()
    {
        $aMatrix = $this->getMatrix();
        $aMatrixChunk = array_chunk($aMatrix, self::SINGLE_WORD_WIDTH);
        $sText = '';

        foreach ($aMatrixChunk as $iOrder => $aMatrixOne) {
            foreach ($aMatrixOne as $x => $aColor) {
                if (isset($aColor[self::WORD_COLOR_VALUE])) {
                    $aMatrixOne[$x] = $aColor[self::WORD_COLOR_VALUE];
                } else {
                    unset($aMatrixOne[$x]);
                }
            }

            $sText .= $this->getTextByMatrix($aMatrixOne);
        }

        return $sText;
    }
}

# end of this file.
