<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2010, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

if (defined('PHPUnit_MAIN_METHOD') === false) {
    define('PHPUnit_MAIN_METHOD', 'PHP_Depend_Issues_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once dirname(__FILE__) . '/KeepTypeInformationForPrimitivesIssue084Test.php';
require_once dirname(__FILE__) . '/NamespaceSupportIssue002Test.php';
require_once dirname(__FILE__) . '/PHPDependCatchesParsingErrorsIssue061Test.php';
require_once dirname(__FILE__) . '/ParserSetsCorrectParametersIssue032Test.php';
require_once dirname(__FILE__) . '/ReflectionCompatibilityIssue067Test.php';
require_once dirname(__FILE__) . '/StoreTokensForAllNodeTypesIssue079Test.php';
require_once dirname(__FILE__) . '/HandlingOfIdeStyleDependenciesInCommentsIssue087Test.php';

/**
 * Test suite for issues meta package.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Issues
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Issues_AllTests
{
    /**
     * Test suite main method.
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates the phpunit test suite for this package.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('PHP_Depend_Issues - AllTests');

        $suite->addTestSuite('PHP_Depend_Issues_NamespaceSupportIssue002Test');
        $suite->addTestSuite('PHP_Depend_Issues_ParserSetsCorrectParametersIssue032Test');
        $suite->addTestSuite('PHP_Depend_Issues_PHPDependCatchesParsingErrorsIssue061Test');
        $suite->addTestSuite('PHP_Depend_Issues_ReflectionCompatibilityIssue067Test');
        $suite->addTestSuite('PHP_Depend_Issues_StoreTokensForAllNodeTypesIssue079Test');
        $suite->addTestSuite('PHP_Depend_Issues_KeepTypeInformationForPrimitivesIssue084Test');
        $suite->addTestSuite('PHP_Depend_Issues_HandlingOfIdeStyleDependenciesInCommentsIssue087Test');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD === 'PHP_Depend_Issues_AllTests::main') {
    PHP_Depend_Issues_AllTests::main();
}
?>
