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

namespace AppBundle\Model\Core\DTO;

/**
 * PaginatableDTO.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class PaginatableDTO
{
    /**
     * @var int
     */
    public $limit = 25;

    /**
     * @var int
     */
    public $offset = 0;
}
