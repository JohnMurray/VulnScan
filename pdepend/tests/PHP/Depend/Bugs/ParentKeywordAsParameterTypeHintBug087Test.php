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
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

/**
 * Test case for the parent keyword type hint bug no #87.
 *
 * http://tracker.pdepend.org/pdepend/issue_tracker/issue/87
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Bugs
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Bugs_ParentKeywordAsParameterTypeHintBug087Test
    extends PHP_Depend_AbstractTest
{
    /**
     * Tests that the parser handles the parent type hint as expected.
     *
     * @return void
     */
    public function testParserSetsExpectedParentTypeHintReference()
    {
        $packages = self::parseSource('bugs/087/' . __FUNCTION__ . '.php');

        $parameter = $packages->current()
            ->getClasses()
            ->current()
            ->getMethods()
            ->current()
            ->getParameters()
            ->current();

        $this->assertSame('Bar', $parameter->getClass()->getName());
    }

    /**
     * Tests that the parser throws an exception when the parent keyword is used
     * within a function signature.
     *
     * @return void
     */
    public function testParserThrowsExpectedExceptionForParentTypeHintInFunction()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_InvalidStateException',
            'The keyword "parent" was used as type hint but the parameter ' .
            'declaration is not in a class scope.'
        );

        self::parseSource('bugs/087/' . __FUNCTION__ . '.php');
    }

    public function testParserThrowsExpectedExceptionForParentTypeHintClassWithoutParent()
    {
        $this->setExpectedException(
            'PHP_Depend_Parser_InvalidStateException',
            'The keyword "parent" was used as type hint but the parent ' .
            'class "Baz" does not declare a parent.'
        );

        self::parseSource('bugs/087/' . __FUNCTION__ . '.php');
    }
}
?>
