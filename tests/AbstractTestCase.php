<?php

/*
 * Laravel-Mns -- 阿里云消息队列（MNS）的 Laravel 适配。
 *
 * This file is part of the abe/laravel-mns.
 *
 * (c) Abraham Greyson <82011220@qq.com>
 * @link: https://github.com/abrahamgreyson/laravel-mns
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LaravelMns\Test;

use Mockery as m;
use PHPUnit_Runner_Version;

abstract class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string phpunit major version string.
     */
    protected $phpunitSeries;

    public function tearDown()
    {
        // 因为运行在 strict 模式下的 phpunit，会将不包含 phpunit 断言的测试方法
        // 标记为 risky。这里简单的在测试方法执行后把 mockery 的预期全部转化成
        // phpunit 的断言，通常不直接包含 phpunit 断言的测试，基本都是 mockery 预期。
        // 测试类需要继承这里， m::close() 的时候替换为 parent::tearDown()。
        if ($container = m::getContainer()) {
            $this->addToAssertionCount(
                $container->mockery_getExpectationCount()
            );
            m::close();
        }
        $this->phpunitSeries = null;
    }

    public function setUp()
    {
        // 取到 phpunit 的版本，并在这个类里做适配防止测试运行在
        // 不同 phpunit 版本下出现的接口不兼容问题。
        $id = PHPUnit_Runner_Version::id();
        if (2 === substr_count($id, '.')) {
            // x.x.x
            $second = strpos($id, '.') + 1;
            $this->phpunitSeries = substr($id, 0, strpos($id, '.', $second));
        } elseif (1 === substr_count($id, '.')) {
            // x.x
            $this->phpunitSeries = $id;
        } else {
            // x
            $this->phpunitSeries = $id . '.0';
        }
        if ('' == $this->phpunitSeries) {
            $this->phpunitSeries = '4.0';
        }
    }

    /**
     * In phpunit 5.2 setExpectedException() method is deprecated.
     *
     * @param mixed $exception
     */
    public function expectException($exception)
    {
        if ($this->phpunitSeries < 5.2) {
            $this->setExpectedException($exception);
        } else {
            parent::expectException($exception);
        }
    }

    public function expectExceptionMessage($message)
    {
        if ($this->phpunitSeries < 5.2) {
            $this->setExpectedException($this->getExpectedException(), $message);
        } else {
            parent::expectExceptionMessage($message);
        }
    }
}
