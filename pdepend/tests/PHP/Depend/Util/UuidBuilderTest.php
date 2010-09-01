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
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://pdepend.org/
 * @since      0.9.12
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Code/File.php';
require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Code/Function.php';
require_once 'PHP/Depend/Util/UuidBuilder.php';

/**
 * Test case for the {@link PHP_Depend_Util_UuidBuilder} class.
 *
 * @category   QualityAssurance
 * @package    PHP_Depend
 * @subpackage Util
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2010 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://pdepend.org/
 * @since      0.9.12
 */
class PHP_Depend_Util_UuidBuilderTest extends PHP_Depend_AbstractTest
{
    /**
     * testBuilderCreatesExpectedIdentifierForFile
     *
     * @return void
     * @covers PHP_Depend_Util_UuidBuilder
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testBuilderCreatesExpectedIdentifierForFile()
    {
        $file    = new PHP_Depend_Code_File(__FILE__);
        $builder = new PHP_Depend_Util_UuidBuilder();

        $this->assertRegExp('/^[a-z0-9]{11}$/', $builder->forFile($file));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForClass
     *
     * @return void
     * @covers PHP_Depend_Util_UuidBuilder
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testBuilderCreatesExpectedIdentifierForClass()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUUID('FooBar');

        $class = new PHP_Depend_Code_Class(__FUNCTION__);
        $class->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-00$/', $builder->forClassOrInterface($class));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForSecondIdenticalClass
     *
     * @return void
     * @covers PHP_Depend_Util_UuidBuilder
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testBuilderCreatesExpectedIdentifierForSecondIdenticalClass()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUUID('FooBar');

        $class = new PHP_Depend_Code_Class(__FUNCTION__);
        $class->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();
        $builder->forClassOrInterface($class);

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-01$/', $builder->forClassOrInterface($class));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForSecondClass
     *
     * @return void
     * @covers PHP_Depend_Util_UuidBuilder
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testBuilderCreatesExpectedIdentifierForSecondClass()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUUID('FooBar');

        $class1 = new PHP_Depend_Code_Class(__FUNCTION__);
        $class1->setSourceFile($file);

        $class2 = new PHP_Depend_Code_Class(__CLASS__);
        $class2->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();
        $builder->forClassOrInterface($class1);

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-00$/', $builder->forClassOrInterface($class2));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForFunction
     *
     * @return void
     * @covers PHP_Depend_Util_UuidBuilder
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testBuilderCreatesExpectedIdentifierForFunction()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUUID('FooBar');

        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-00$/', $builder->forFunction($function));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForMethod
     *
     * @return void
     * @covers PHP_Depend_Util_UuidBuilder
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testBuilderCreatesExpectedIdentifierForMethod()
    {
        $class = new PHP_Depend_Code_Class(__CLASS__);
        $class->setUUID('FooBar');

        $method = new PHP_Depend_Code_Method(__FUNCTION__);
        $method->setParent($class);

        $builder = new PHP_Depend_Util_UuidBuilder();

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}$/', $builder->forMethod($method));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForSecondIdenticalFunction
     *
     * @return void
     * @covers PHP_Depend_Util_UuidBuilder
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testBuilderCreatesExpectedIdentifierForSecondIdenticalFunction()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUUID('FooBar');

        $function = new PHP_Depend_Code_Function(__FUNCTION__);
        $function->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();
        $builder->forFunction($function);

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-01$/', $builder->forFunction($function));
    }

    /**
     * testBuilderCreatesExpectedIdentifierForSecondFunction
     *
     * @return void
     * @covers PHP_Depend_Util_UuidBuilder
     * @group pdepend
     * @group pdepend::util
     * @group unittest
     */
    public function testBuilderCreatesExpectedIdentifierForSecondFunction()
    {
        $file = new PHP_Depend_Code_File(__FILE__);
        $file->setUUID('FooBar');

        $function1 = new PHP_Depend_Code_Function(__FUNCTION__);
        $function1->setSourceFile($file);

        $function2 = new PHP_Depend_Code_Function(__CLASS__);
        $function2->setSourceFile($file);

        $builder = new PHP_Depend_Util_UuidBuilder();
        $builder->forFunction($function1);

        $this->assertRegExp('/^FooBar\-[a-z0-9]{11}\-00$/', $builder->forFunction($function2));
    }
}