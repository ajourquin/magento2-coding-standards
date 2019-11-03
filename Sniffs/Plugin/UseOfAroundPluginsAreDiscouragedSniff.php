<?php declare(strict_types = 1);

namespace Ajourquin\Sniffs\Plugin;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class UseOfAroundPluginsAreDiscouragedSniff implements Sniff
{
    private const AROUND_PLUGIN_PREFIX = 'around';

    /**
     * @return array
     */
    public function register() : array
    {
        return [T_FUNCTION];
    }

    /**
     * @param File $phpcsFile
     * @param $stackPtr
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $functionName = $phpcsFile->getDeclarationName($stackPtr);

        if (substr($functionName, 0, strlen(self::AROUND_PLUGIN_PREFIX)) === self::AROUND_PLUGIN_PREFIX) {
            $phpcsFile->addError(
                'Around plugins are discouraged.',
                $stackPtr,
                'AroundPluginFound'
            );
        }
    }
}
