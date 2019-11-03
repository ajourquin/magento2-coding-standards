<?php declare(strict_types = 1);

namespace Ajourquin\Sniffs\Classes;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ZendComponentSniff implements Sniff
{
    /**
     * String representation of warning.
     *
     * @var string
     */
    protected $errorMessage = 'The direct use of Zend Framework components is discouraged. Use Magento Framework instead.';

    /**
     * Error violation code.
     *
     * @var string
     */
    protected $errorCode = 'Zend Component Found';


    /**
     * @inheritdoc
     */
    public function register() : array
    {
        return [T_NEW, T_DOUBLE_COLON, T_FUNCTION];
    }

    /**
     * @inheritdoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        switch ($tokens[$stackPtr]['type']) {
            case 'T_NEW':
                // Check new \Zend_xxx
                $newInstanciationPosition = $phpcsFile->findNext(T_STRING, $stackPtr + 1);
                if ($this->containsZendString($tokens[$newInstanciationPosition]['content'])) {
                    $phpcsFile->addError($this->errorMessage, $stackPtr, $this->errorCode);
                }
                break;
            case 'T_DOUBLE_COLON':
                // Check static Zend_xxx::
                $staticInstanciationPosition = $phpcsFile->findPrevious(T_STRING, $stackPtr - 1);
                if ($this->containsZendString($tokens[$staticInstanciationPosition]['content'])) {
                    $phpcsFile->addError($this->errorMessage, $stackPtr, $this->errorCode);
                }
                break;
            case 'T_FUNCTION':
                // Check in DI
                $functionName = $phpcsFile->getDeclarationName($stackPtr);
                if ($functionName === '__construct') {
                    $params = $phpcsFile->getMethodParameters($stackPtr);
                    foreach ($params as $param) {
                        if ($this->containsZendString($param['content'])) {
                            $phpcsFile->addError($this->errorMessage, $stackPtr, $this->errorCode);
                        }
                    }
                }
                break;
        }
    }

    /**
     * @param $string
     * @return bool
     */
    private function containsZendString($string) : bool
    {
        if (substr(str_replace('\\', '', $string), 0, 4) === 'Zend') {
            return true;
        }

        return false;
    }
}
