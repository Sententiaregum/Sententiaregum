<?php

/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace AppBundle\Tests\Unit\Fixtures;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Simple fixture that helps testing file uploads with the dto converter.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 */
class FileUploadDTO
{
    private $file;

    public function setFile(UploadedFile $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }
}
