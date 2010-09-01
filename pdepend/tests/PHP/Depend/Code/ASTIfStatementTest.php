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
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTIfStatement.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTIfStatement} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTIfStatementTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testHasElseMethodReturnsFalseByDefault
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testHasElseMethodReturnsFalseByDefault()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertFalse($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseIfBranchExists
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testHasElseMethodReturnsTrueWhenElseIfBranchExists()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseBranchWithIfExists
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testHasElseMethodReturnsTrueWhenElseBranchWithIfExists()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }

    /**
     * testHasElseMethodReturnsTrueWhenElseBranchExists
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testHasElseMethodReturnsTrueWhenElseBranchExists()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertTrue($stmt->hasElse());
    }
    
    /**
     * Tests the generated object graph of an if statement.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIfStatementGraphWithBooleanExpressions()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(2, count($stmt->getChildren()));
    }

    /**
     * testFirstChildOfIfStatementIsInstanceOfExpression
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testFirstChildOfIfStatementIsInstanceOfExpression()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTExpression::CLAZZ, $stmt->getChild(0));
    }

    /**
     * testSecondChildOfIfStatementIsInstanceOfScopeStatement
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testSecondChildOfIfStatementIsInstanceOfScopeStatement()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt->getChild(1));
    }

    /**
     * Tests the start line value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIfStatementHasExpectedStartLine()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(4, $stmt->getStartLine());
    }

    /**
     * Tests the start column value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIfStatementHasExpectedStartColumn()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getStartColumn());
    }

    /**
     * Tests the end line value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIfStatementHasExpectedEndLine()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(6, $stmt->getEndLine());
    }

    /**
     * Tests the end column value.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIfStatementHasExpectedEndColumn()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(5, $stmt->getEndColumn());
    }

    /**
     * testIfStatementWithElseContainsExpectedNumberOfChildNodes
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testIfStatementWithElseContainsExpectedNumberOfChildNodes()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertEquals(3, count($stmt->getChildren()));
    }

    /**
     * testThirdChildOfIfStatementIsInstanceOfScopeStatementForElse
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testThirdChildOfIfStatementIsInstanceOfScopeStatementForElse()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTScopeStatement::CLAZZ, $stmt->getChild(2));
    }

    /**
     * testThirdChildOfIfStatementIsInstanceOfExpressionForElseIf
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTIfStatement
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testThirdChildOfIfStatementIsInstanceOfIfStatementForElseIf()
    {
        $stmt = $this->_getFirstIfStatementInFunction(__METHOD__);
        $this->assertType(PHP_Depend_Code_ASTIfStatement::CLAZZ, $stmt->getChild(2));
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTIfStatement
     */
    private function _getFirstIfStatementInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTIfStatement::CLAZZ
        );
    }
}