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

namespace AppBundle\Fixer;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * Custom fixer for PHP's `T_DECLARE` tokens to fix an incompatibility issue between PHPCS and StyleCI.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class StrictTypeDeclarationFixer extends AbstractFixer
{
    const TOKENS_FROM_DECLARE_TO_ASSIGNMENT = 3;
    const EQUAL_SIGN                        = '=';

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        foreach ($tokens->findGivenKind(T_DECLARE) as $index => $token) {
            $whitespace = $tokens[$index + 1];
            if ($whitespace->isWhitespace()) {
                $whitespace->clear();
            }

            // jump to the third non-whitespace token (the third one is the equal sign to be fixed);
            for ($i = 0, $assignmentTokenIndex = $index; $i < self::TOKENS_FROM_DECLARE_TO_ASSIGNMENT; $i++) {
                $assignmentTokenIndex = $tokens->getNextNonWhitespace($assignmentTokenIndex);
            }

            // analyze before and after token
            $assignmentToken = $tokens[$assignmentTokenIndex];
            if (self::EQUAL_SIGN === $assignmentToken->getContent()) {
                $before = $tokens[$assignmentTokenIndex - 1];
                $after  = $tokens[$assignmentTokenIndex + 1];

                // clear tokens before and afterwords if they're whitespaces
                /* @var \Symfony\CS\Tokenizer\Token $token */
                foreach ([$before, $after] as $trailingToken) {
                    if ($trailingToken->isWhitespace()) {
                        $trailingToken->clear();
                    }
                }
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Custom `declare` fixer for `strict_types` declarations!';
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return self::NONE_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // after the BracesFixer and OperatorsSpacesFixer
        return -30;
    }
}
