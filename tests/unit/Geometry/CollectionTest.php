<?php
/**
 * This file contains the CollectionTest class.
 * For more information see the class description below.
 *
 * @author Peter Bathory <peter.bathory@cartographia.hu>
 * @since 2020-03-19
 */

namespace geoPHP\Tests\Geometry;

use geoPHP\Geometry\Collection;
use geoPHP\Geometry\Point;
use geoPHP\Geometry\LineString;
use geoPHP\Geometry\Polygon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * This class... TODO: Complete this
 */
class CollectionTest extends TestCase
{

    /**
     * @throws \Exception
     */
    public static function providerIs3D(): array
    {
        return [
                [[new Point(1, 2)], false],
                [[new Point(1, 2, 3)], true],
                [[new Point(1, 2, 3), new Point(1, 2)], true],
        ];
    }

    /**
     *
     * @param Point[] $components
     * @param bool $result
     * @throws Exception
     * @throws Exception
     */
    #[DataProvider('providerIs3D')]
    public function testIs3D(array $components, bool $result): void
    {
        /** @var Collection $stub */
        $stub = $this->getMockForAbstractClass(Collection::class, [$components, true]);
        $this->assertEquals($stub->is3D(), $result);
    }

    /**
     * @throws \Exception
     */
    public static function providerIsMeasured(): array
    {
        return [
                [[new Point()], false],
                [[new Point(1, 2)], false],
                [[new Point(1, 2, 3)], false],
                [[new Point(1, 2, 3, 4)], true],
                [[new Point(1, 2, 3, 4), new Point(1, 2)], true],
        ];
    }

    /**
     * @param Point[] $components
     * @param bool $result
     * @throws Exception
     * @throws Exception
     */
    #[DataProvider('providerIsMeasured')]
    public function testIsMeasured(array $components, bool $result): void
    {
        /** @var Collection $stub */
        $stub = $this->getMockForAbstractClass(Collection::class, [$components, true]);
        $this->assertEquals($result, $stub->isMeasured());
    }

    /**
     * @throws \Exception
     */
    public static function providerIsEmpty(): array
    {
        return [
                [[], true],
                [[new Point()], true],
                [[new Point(1, 2)], false],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerIsEmpty')]
    public function testIsEmpty(array $components, bool $result): void
    {
        /** @var Collection $stub */
        $stub = $this->getMockForAbstractClass(Collection::class, [$components, true]);

        $this->assertEquals($stub->isEmpty(), $result);
    }

    /**
     * @throws Exception
     */
    public function testNonApplicableMethods(): void
    {
        /** @var Collection $stub */
        $stub = $this->getMockForAbstractClass(Collection::class, [[], true]);

        $this->assertNull($stub->x());
        $this->assertNull($stub->y());
        $this->assertNull($stub->z());
        $this->assertNull($stub->m());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testAsArray(): void
    {
        $components = [
                new Point(1, 2),
                new LineString()
        ];
        $expected = [
                [1, 2],
                []
        ];

        /** @var Collection $stub */
        $stub = $this->getMockForAbstractClass(Collection::class, [$components, true]);

        $this->assertEquals($stub->asArray(), $expected);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testFlatten(): void
    {
        $components = [
                new Point(1, 2, 3, 4),
                new Point(5, 6, 7, 8),
                new LineString([new Point(1, 2, 3, 4), new Point(5, 6, 7, 8)]),
        ];

        /** @var Collection $stub */
        $stub = $this->getMockForAbstractClass(Collection::class, [$components]);
        $stub->flatten();

        $this->assertFalse($stub->hasZ());
        $this->assertFalse($stub->isMeasured());
        $this->assertFalse($stub->getPoints()[0]->hasZ());
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function testExplode(): void
    {
        $points = [new Point(1, 2), new Point(3, 4), new Point(5, 6), new Point(1, 2)];
        $components = [
                new Polygon([new LineString($points)])
        ];

        /** @var Collection $stub */
        $stub = $this->getMockForAbstractClass(Collection::class, [$components]);

        $segments = $stub->explode();
        $this->assertCount(count($points) - 1, $segments);
        foreach ($segments as $i => $segment) {
            $this->assertCount(2, $segment->getComponents());

            $this->assertSame($segment->startPoint(), $points[$i]);
            $this->assertSame($segment->endPoint(), $points[$i + 1]);
        }
    }
}