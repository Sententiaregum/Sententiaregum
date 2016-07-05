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
 * StrictTypeDeclarationFixer.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class StrictTypeDeclarationFixer extends AbstractFixer
{
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

            $assignmentTokenIndex = $this->jumpWhitespaces($tokens, $index, 3);
            $assignmentToken      = $tokens[$assignmentTokenIndex];

            if ('=' === $assignmentToken->getContent()) {
                $before = $tokens[$assignmentTokenIndex - 1];
                $after  = $tokens[$assignmentTokenIndex + 1];

                if ($before->isWhitespace()) {
                    $before->clear();
                }
                if ($after->isWhitespace()) {
                    $after->clear();
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

    /**
     * Jumps til the next token with maximum range.
     *
     * @param Tokens $tokens
     * @param int    $index
     * @param int    $end
     *
     * @return int
     */
    private function jumpWhitespaces(Tokens $tokens, int $index, int $end): int
    {
        for ($i = 0; $i < $end; $i++) {
            $index = $tokens->getNextNonWhitespace($index);
        }

        return $index;
    }
}
