<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Tests\Unit\Fixtures;

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
