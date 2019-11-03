<?php declare(strict_types = 1);

namespace Ajourquin\Sniffs\Classes;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;


class ObjectManagerSniff implements Sniff
{
    /**
     * String representation of warning.
     *
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $errorMessage = 'The direct use of ObjectManager is forbidden. Inject necessary dependencies via constructor.';

    /**
     * Error violation code.
     *
     * @var string
     */
    protected $errorCode = 'ObjectManagerFound';

    /**
     * Possible names of ObjectManager variable or methods from where it can be called.
     *
     * @var array
     */
    protected $objectManagerNames = [
        'om',
        '_om',
        'objectmanager',
        '_objectmanager',
        'getobjectmanager',
    ];

    /**
     * ObjectManager methods we are looking for.
     *
     * @var array
     */
    protected $objectManagerMethods = [
        'get',
        'create',
        'getInstance',
    ];

    /**
     * @inheritdoc
     */
    public function register() : array
    {
        return [T_OBJECT_OPERATOR, T_DOUBLE_COLON];
    }

    /**
     * @inheritdoc
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $methodPosition = $phpcsFile->findNext(T_STRING, $stackPtr + 1);

        if ($methodPosition !== false &&
            in_array($tokens[$methodPosition]['content'], $this->objectManagerMethods)
        ) {
            $objectManagerPosition = $phpcsFile->findPrevious([T_STRING, T_VARIABLE], $stackPtr - 1);
            if ($objectManagerPosition !== false) {
                $objectManagerName = strtolower($tokens[$objectManagerPosition]['content']);
                if ($tokens[$objectManagerPosition]['code'] === T_VARIABLE) {
                    $objectManagerName = substr($objectManagerName, 1);
                }
                if (in_array($objectManagerName, $this->objectManagerNames)) {
                    $phpcsFile->addError($this->errorMessage, $stackPtr, $this->errorCode);
                }
            }
        }
    }
}
