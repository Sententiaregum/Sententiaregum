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

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Model\User\DTO\LocaleSwitcherDTO;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class LocaleController extends BaseController
{
    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Creates a list of all supported languages: ['de' => 'Deutsch', ...]",
     *     statusCodes={200="Successful generation of locale config"},
     *     requirements={
     *         {"name"="_format", "dataType"="string", "requirement"="^(json|xml)$", "description"="Data format to return"}
     *     }
     * )
     *
     * Controller action that returns all supported languages.
     * The result format will look like this:
     * [ 'de' => 'Deutsch', ... ]
     *
     * @return string[]
     *
     * @Rest\Get("/locale.{_format}", name="app.language.supported_locales", requirements={"_format"="^(json|xml)$"})
     * @Rest\View
     */
    public function getSupportedLocalesAction(): array
    {
        return $this->getLocales();
    }

    /**
     * @ApiDoc(
     *     resource=true,
     *     description="Switches the locale of a user",
     *     statusCodes={204="Successfully updated",401="Unauthorized",400="Invalid locale"},
     *     parameters={
     *         {"name"="locale", "dataType"="string", "required"=true, "description"="New locale of a user"}
     *     },
     *     requirements={
     *         {"name"="_format", "dataType"="string", "requirement"="^(json|xml)$", "description"="Data format to return"}
     *     }
     * )
     *
     * Switches the locale property of a user.
     *
     * @param LocaleSwitcherDTO $localeSwitcherDTO
     *
     * @Rest\Patch("/protected/locale.{_format}", name="app.language.switch_locale", requirements={"_format"="^(json|xml)$"})
     * @ParamConverter(name="localeSwitcherDTO", class="AppBundle\Model\User\DTO\LocaleSwitcherDTO")
     *
     * @Rest\View(statusCode=204)
     */
    public function switchLocaleAction(LocaleSwitcherDTO $localeSwitcherDTO)
    {
        /** @var \Symfony\Component\Validator\Validator\ValidatorInterface $validator */
        $validator = $this->get('validator');

        if (count($validator->validate($localeSwitcherDTO)) > 0) {
            throw new HttpException(Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getCurrentUser();

        if ($user->getLocale() !== $newLocale = $localeSwitcherDTO->getLocale()) {
            $em = $this->getDoctrine()->getManager();

            $user->modifyUserLocale($newLocale);

            $em->persist($user);
            $em->flush();
        }
    }
}
