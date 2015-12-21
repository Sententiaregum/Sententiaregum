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

namespace AppBundle\Controller\User;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class OnlineUsersController extends Controller
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Creates a list of followers that contains a list showing which followers are online",
     *     statusCodes={200="Successful generation","401"="Unauthorized"}
     * )
     *
     * Controller action that creates a list of users the current user follows that shows which users are online.
     *
     * @return bool[]
     *
     * @Rest\Get("/protected/online-users.{_format}", name="app.user.online")
     * @Rest\View
     */
    public function onlineFollowingListAction()
    {
        /** @var \AppBundle\Model\User\User $currentUser */
        $currentUser = $this->getUser();
        /** @var \AppBundle\Model\User\Online\OnlineUserIdDataProviderInterface $cluster */
        $cluster        = $this->get('app.redis.cluster.online_users');
        $userRepository = $this->getDoctrine()->getRepository('Account:User');

        return $cluster->validateUserIds($userRepository->getFollowingIdsByUser($currentUser));
    }
}
