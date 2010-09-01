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
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

require_once dirname(__FILE__) . '/../AbstractTest.php';

require_once 'PHP/Depend/Code/Class.php';
require_once 'PHP/Depend/Metrics/Hierarchy/Analyzer.php';

/**
 * Test case for the hierarchy analyzer.
 *
 * @category  QualityAssurance
 * @package   PHP_Depend
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2008-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class PHP_Depend_Metrics_Hierarchy_AnalyzerTest extends PHP_Depend_Metrics_AbstractTest
{
    /**
     * testCalculatesExpectedNumberOfLeafClasses
     *
     * @return void
     * @covers PHP_Depend_Metrics_Hierarchy_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::hierarchy
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfLeafClasses()
    {
        $analyzer = new PHP_Depend_Metrics_Hierarchy_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(2, $metrics['leafs']);
    }

    /**
     * testCalculatesExpectedNumberOfAbstractClasses
     *
     * @return void
     * @covers PHP_Depend_Metrics_Hierarchy_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::hierarchy
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfAbstractClasses()
    {
        $analyzer = new PHP_Depend_Metrics_Hierarchy_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(1, $metrics['clsa']);
    }

    /**
     * testCalculatesExpectedNumberOfConcreteClasses
     *
     * @return void
     * @covers PHP_Depend_Metrics_Hierarchy_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::hierarchy
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfConcreteClasses()
    {
        $analyzer = new PHP_Depend_Metrics_Hierarchy_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(2, $metrics['clsc']);
    }

    /**
     * testCalculatesExpectedNumberOfRootClasses
     *
     * @return void
     * @covers PHP_Depend_Metrics_Hierarchy_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::hierarchy
     * @group unittest
     */
    public function testCalculatesExpectedNumberOfRootClasses()
    {
        $analyzer = new PHP_Depend_Metrics_Hierarchy_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(1, $metrics['roots']);
    }

    /**
     * testCalculatedLeafsMetricDoesNotContainNotUserDefinedClasses
     *
     * @return void
     * @covers PHP_Depend_Metrics_Hierarchy_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::hierarchy
     * @group unittest
     */
    public function testCalculatedLeafsMetricDoesNotContainNotUserDefinedClasses()
    {
        $analyzer = new PHP_Depend_Metrics_Hierarchy_Analyzer();
        $analyzer->analyze(self::parseTestCaseSource(__METHOD__));

        $metrics = $analyzer->getProjectMetrics();
        $this->assertEquals(0, $metrics['leafs']);
    }

    /**
     * Tests that {@link PHP_Depend_Metrics_Hierarchy_Analyzer::getNodeMetrics()}
     * returns an empty <b>array</b> for an unknown node uuid.
     *
     * @return void
     * @covers PHP_Depend_Metrics_Hierarchy_Analyzer
     * @group pdepend
     * @group pdepend::metrics
     * @group pdepend::metrics::hierarchy
     * @group unittest
     */
    public function testGetNodeMetricsForUnknownUUID()
    {
        $class    = new PHP_Depend_Code_Class('PDepend');
        $analyzer = new PHP_Depend_Metrics_Hierarchy_Analyzer();

        $this->assertSame(array(), $analyzer->getNodeMetrics($class));
    }
}
