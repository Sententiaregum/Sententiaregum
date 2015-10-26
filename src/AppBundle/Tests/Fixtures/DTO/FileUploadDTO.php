<?php

/*
 * This file is part of the sententiaregum application.
 *
 * Sententiaregum is a social network based on Symfony2 and BackboneJS/ReactJS
 *
 * @copyright (c) 2015 Sententiaregum
 * Please check out the license file in the document root of this application
 */

namespace AppBundle\Tests\Fixtures\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Simple fixture that helps testing file uploads with the dto converter.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class FileUploadDTO
{
    private $file;

    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }
}
