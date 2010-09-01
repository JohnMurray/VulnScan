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

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Abstract base class for issue tests.
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
abstract class PHP_Depend_Issues_AbstractTest extends PHP_Depend_AbstractTest
{
    /**
     * Returns the parameters of the first function in the test case file.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    protected function getParametersOfFirstFunction()
    {
        $packages = self::parseTestCase();
        return $packages->current()
            ->getFunctions()
            ->current()
            ->getParameters();
    }
    
    /**
     * Parses the sourse for the calling test case.
     *
     * @param string $testCase Optional test case name.
     *
     * @return PHP_Depend_Code_NodeIterator
     */
    protected static function parseTestCase($testCase = null)
    {
        if ($testCase === null) {
            $testCase = self::getTestCaseMethod();
        }

        list($class, $method) = explode('::', $testCase);
        if (preg_match('([^\d](\d+)Test$)', $class, $match) === 0) {
            throw new ErrorException('Unexpected class name format');
        }
        return self::parseSource('issues/' . $match[1] . '/' . $method . '.php');
    }

    /**
     * Returns a php callback for the calling test case method.
     *
     * @return string
     */
    protected static function getTestCaseMethod()
    {
        $trace = debug_backtrace();
        foreach ($trace as $frame) {
            if (strpos($frame['function'], 'test') === 0) {
                return $frame['class'] . '::' . $frame['function'];
            }
        }
        throw new ErrorException('Cannot locate test case method.');
    }
}