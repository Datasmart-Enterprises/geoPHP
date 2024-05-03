<?php

namespace geoPHP\Tests\Geometry;

use Exception;
use geoPHP\Exception\InvalidGeometryException;
use geoPHP\Geometry\GeometryCollection;
use geoPHP\Geometry\LineString;
use geoPHP\Geometry\Point;
use geoPHP\Geometry\MultiPoint;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use geoPHP\Geometry\MultiGeometry;
use geoPHP\Geometry\Geometry;

/**
 * Unit tests of MultiPoint geometry
 */
#[Group('geometry')]
class MultiPointTest extends TestCase
{

    /**
     * @throws Exception
     */
    public static function providerValidComponents(): array
    {
        return [
            [[]],                                   // no components, empty MultiPoint
            [[new Point()]],                        // empty component
            [[new Point(1, 2)]],
            [[new Point(1, 2), new Point(3, 4)]],
            [[new Point(1, 2, 3, 4), new Point(5, 6, 7, 8)]],
        ];
    }

    /**
     * @param Point[] $points
     * @throws Exception
     */
    #[DataProvider('providerValidComponents')]
    public function testValidComponents(array $points): void
    {
        $this->assertNotNull(new MultiPoint($points));
    }

    /**
     * @throws Exception
     */
    public static function providerInvalidComponents(): array
    {
        return [
            [[LineString::fromArray([[1,2],[3,4]])]],  // wrong component type
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerInvalidComponents')]
    public function testConstructorWithInvalidComponents(mixed $components): void
    {
        $this->expectException(InvalidGeometryException::class);

        new MultiPoint($components);
    }

    public function testGeometryType(): void
    {
        $multiPoint = new MultiPoint();

        $this->assertEquals(Geometry::MULTI_POINT, $multiPoint->geometryType());

        $this->assertInstanceOf(MultiPoint::class, $multiPoint);
        $this->assertInstanceOf(MultiGeometry::class, $multiPoint);
        $this->assertInstanceOf(Geometry::class, $multiPoint);
    }

    /**
     * @throws Exception
     */
    public function testIs3D(): void
    {
        $this->assertTrue( (new Point(1, 2, 3))->is3D() );
        $this->assertTrue( (new Point(1, 2, 3, 4))->is3D() );
        $this->assertTrue( (new Point(null, null, 3, 4))->is3D() );
    }

    /**
     * @throws Exception
     */
    public function testIsMeasured(): void
    {
        $this->assertTrue( (new Point(1, 2, null, 4))->isMeasured() );
        $this->assertTrue( (new Point(null, null , null, 4))->isMeasured() );
    }

    public static function providerCentroid(): array
    {
        return [
            [[], []],
            [[[0, 0], [0, 10]], [0, 5]]
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerCentroid')]
    public function testCentroid(array $components, array $centroid): void
    {
        $multiPoint = MultiPoint::fromArray($components);

        $this->assertEquals($multiPoint->centroid(), Point::fromArray($centroid));
    }

    public static function providerIsSimple(): array
    {
        return [
            [[], true],
            [[[0, 0], [0, 10]], true],
            [[[1, 1], [2, 2], [1, 3], [1, 2], [2, 1]], true],
            [[[0, 10], [0, 10]], false],
        ];
    }

    #[DataProvider('providerIsSimple')]
    public function testIsSimple(array $points, bool $result): void
    {
        $multiPoint = MultiPoint::fromArray($points);

        $this->assertSame($multiPoint->isSimple(), $result);
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerValidComponents')]
    public function testNumPoints(array $points): void
    {
        $multiPoint = new MultiPoint($points);

        $this->assertEquals($multiPoint->numPoints(), $multiPoint->numGeometries());
    }

    public function testTrivialAndNotValidMethods(): void
    {
        $point = new MultiPoint();

        $this->assertSame( $point->dimension(), 0 );

        $this->assertEquals( $point->boundary(), new GeometryCollection() );

        $this->assertNull( $point->explode());

        $this->assertTrue( $point->isSimple());
    }

}
