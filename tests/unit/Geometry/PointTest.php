<?php

namespace geoPHP\Tests\Geometry;

use Exception;
use geoPHP\Exception\InvalidGeometryException;
use geoPHP\Geometry\Geometry;
use geoPHP\Geometry\GeometryCollection;
use geoPHP\Geometry\LineString;
use geoPHP\Geometry\MultiPoint;
use geoPHP\Geometry\Point;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests of Point geometry
 */
#[Group('geometry')]
class PointTest extends TestCase
{

    public static function providerValidCoordinatesXY(): array
    {
        return [
            'null coordinates' => [0, 0],
            'positive integer' => [10, 20],
            'negative integer' => [-10, -20],
            'WGS84'            => [47.1234056789, 19.9876054321],
            'HD72/EOV'         => [238084.12, 649977.59],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerValidCoordinatesXY')]
    public function testValidCoordinatesXY(float|int $x, float|int $y): void
    {
        $point = new Point($x, $y);

        $this->assertEquals($x, $point->x());
        $this->assertEquals($y, $point->y());
        $this->assertNull($point->z());
        $this->assertNull($point->m());

        $this->assertIsFloat($point->x());
        $this->assertIsFloat($point->y());
    }

    public static function providerValidCoordinatesXYZ_or_XYM(): array
    {
        return [
            'null coordinates' => [0, 0, 0],
            'positive integer' => [10, 20, 30],
            'negative integer' => [-10, -20, -30],
            'WGS84'            => [47.1234056789, 19.9876054321, 100.1],
            'HD72/EOV'         => [238084.12, 649977.59, 56.38],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerValidCoordinatesXYZ_or_XYM')]
    public function testValidCoordinatesXYZ(float|int $x, float|int $y, float|int $z): void
    {
        $point = new Point($x, $y, $z);

        $this->assertEquals($x, $point->x());
        $this->assertEquals($y, $point->y());
        $this->assertEquals($z, $point->z());
        $this->assertNull($point->m());

        $this->assertIsFloat($point->x());
        $this->assertIsFloat($point->y());
        $this->assertIsFloat($point->z());
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerValidCoordinatesXYZ_or_XYM')]
    public function testValidCoordinatesXYM(float|int $x, float|int $y, float|int $m): void
    {
        $point = new Point($x, $y, null, $m);

        $this->assertEquals($x, $point->x());
        $this->assertEquals($y, $point->y());
        $this->assertEquals($m, $point->m());
        $this->assertNull($point->z());

        $this->assertIsFloat($point->x());
        $this->assertIsFloat($point->y());
        $this->assertIsFloat($point->m());
    }

    public static function providerValidCoordinatesXYZM(): array
    {
        return [
            'null coordinates' => [0, 0, 0, 0],
            'positive integer' => [10, 20, 30, 40],
            'negative integer' => [-10, -20, -30, -40],
            'WGS84'            => [47.1234056789, 19.9876054321, 100.1, 0.00001],
            'HD72/EOV'         => [238084.12, 649977.59, 56.38, -0.00001],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerValidCoordinatesXYZM')]
    public function testValidCoordinatesXYZM(float|int $x, float|int $y, float|int $z, float|int $m): void
    {
        $point = new Point($x, $y, $z, $m);

        $this->assertEquals($x, $point->x());
        $this->assertEquals($y, $point->y());
        $this->assertEquals($z, $point->z());
        $this->assertEquals($m, $point->m());

        $this->assertIsFloat($point->x());
        $this->assertIsFloat($point->y());
        $this->assertIsFloat($point->z());
        $this->assertIsFloat($point->m());
    }

    public function testConstructorWithoutParameters(): void
    {
        $point = new Point();

        $this->assertTrue($point->isEmpty());

        $this->assertNull($point->x());
        $this->assertNull($point->y());
        $this->assertNull($point->z());
        $this->assertNull($point->m());
    }

    public static function providerEmpty(): array
    {
        return [
            'no coordinates'     => [],
            'x is null'          => [null, 20],
            'y is null'          => [10, null],
            'x and y is null'    => [null, null, 30],
            'x, y, z is null'    => [null, null, null, 40],
            'x, y, z, m is null' => [null, null, null, null],
        ];
    }

    /**
     * @param float|int|null $x
     * @param float|int|null $y
     * @param float|int|null $z
     * @param float|int|null $m
     * @throws Exception
     */
    #[DataProvider('providerEmpty')]
    public function testEmpty(float|int $x = null, float|int $y = null, float|int $z = null, float|int $m = null): void
    {
        $point = new Point($x, $y, $z, $m);

        $this->assertTrue($point->isEmpty());

        $this->assertNull($point->x());
        $this->assertNull($point->y());
        $this->assertNull($point->z());
        $this->assertNull($point->m());
    }

    public static function providerInvalidCoordinates(): array
    {
        return [
            'string coordinates'  => ['x', 'y'],
            'boolean coordinates' => [true, false],
            'z is string'         => [1, 2, 'z'],
            'm is string'         => [1, 2, 3, 'm'],
        ];
    }

    /**
     *
     * @param mixed|null $z
     * @param mixed|null $m
     * @throws Exception
     */
    #[DataProvider('providerInvalidCoordinates')]
    public function testConstructorWithInvalidCoordinates(mixed $x, mixed $y, mixed $z = null, mixed $m = null): void
    {
        $this->expectException(InvalidGeometryException::class);

        new Point($x, $y, $z, $m);
    }

    public function testGeometryType(): void
    {
        $point = new Point();

        $this->assertEquals(Geometry::POINT, $point->geometryType());

        $this->assertInstanceOf(Point::class, $point);
        $this->assertInstanceOf(Geometry::class, $point);
    }

    public static function providerIs3D(): array
    {
        return [
            '2 coordinates is not 3D'   => [false, 1, 2],
            '3 coordinates'             => [true, 1, 2, 3],
            '4 coordinates'             => [true, 1, 2, 3, 4],
            'x, y is null but z is not' => [true, null, null, 3, 4],
            'z is null'                 => [false, 1, 2, null, 4],
            'empty point'               => [false],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerIs3D')]
    public function testIs3D($result, $x = null, $y = null, $z = null, $m = null): void
    {
        $this->assertSame($result, (new Point($x, $y, $z, $m))->is3D());
    }

    public static function providerIsMeasured(): array
    {
        return [
            '2 coordinates is false'    => [false, 1, 2],
            '3 coordinates is false'    => [false, 1, 2, 3],
            '4 coordinates'             => [true, 1, 2, 3, 4],
            'x, y is null but m is not' => [true, null, null, 3, 4],
            'm is null'                 => [false, 1, 2, 3, null],
            'empty point'               => [false],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerIsMeasured')]
    public function testIsMeasured($result, $x = null, $y = null, $z = null, $m = null): void
    {
        $this->assertSame($result, (new Point($x, $y, $z, $m))->isMeasured());
    }

    /**
     * @throws Exception
     */
    public function testGetComponents(): void
    {
        $point = new Point(1, 2);
        $components = $point->getComponents();

        $this->assertIsArray($components);
        $this->assertCount(1, $components);
        $this->assertSame($point, $components[0]);
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerValidCoordinatesXYZM')]
    public function testInvertXY(float|int $x, float|int $y, float|int $z, float|int $m): void
    {
        $point = new Point($x, $y, $z, $m);
        $originalPoint = clone $point;
        $point->invertXY();

        $this->assertEquals($x, $point->y());
        $this->assertEquals($y, $point->x());
        $this->assertEquals($z, $point->z());
        $this->assertEquals($m, $point->m());

        $point->invertXY();
        $this->assertEquals($point, $originalPoint);
    }

    /**
     * @throws Exception
     */
    public function testCentroidIsThePointItself(): void
    {
        $point = new Point(1, 2, 3, 4);
        $this->assertSame($point, $point->centroid());
    }

    /**
     * @throws Exception
     */
    public function testBBox(): void
    {
        $point = new Point(1, 2);
        $this->assertSame($point->getBBox(), [
                'maxy' => 2.0,
                'miny' => 2.0,
                'maxx' => 1.0,
                'minx' => 1.0,
        ]);
    }

    /**
     * @throws Exception
     */
    public function testAsArray(): void
    {
        $pointAsArray = (new Point())->asArray();
        $this->assertCount(2, $pointAsArray);
        $this->assertNan($pointAsArray[0]);
        $this->assertNan($pointAsArray[1]);

        $pointAsArray = (new Point(1, 2))->asArray();
        $this->assertSame($pointAsArray, [1.0, 2.0]);

        $pointAsArray = (new Point(1, 2, 3))->asArray();
        $this->assertSame($pointAsArray, [1.0, 2.0, 3.0]);

        $pointAsArray = (new Point(1, 2, null, 3))->asArray();
        $this->assertSame($pointAsArray, [1.0, 2.0, null, 3.0]);

        $pointAsArray = (new Point(1, 2, 3, 4))->asArray();
        $this->assertSame($pointAsArray, [1.0, 2.0, 3.0, 4.0]);
    }

    /**
     * @throws Exception
     */
    public function testBoundary(): void
    {
        $this->assertEquals((new Point(1, 2))->boundary(), new GeometryCollection());
    }

    /**
     * @throws Exception
     */
    public function testEquals(): void
    {
        $this->assertTrue((new Point())->equals(new Point()));

        $point = new Point(1, 2, 3, 4);
        $this->assertTrue($point->equals(new Point(1, 2, 3, 4)));

        $this->assertTrue($point->equals(new Point(1.0000000001, 2.0000000001, 3, 4)));
        $this->assertTrue($point->equals(new Point(0.9999999999, 1.9999999999, 3, 4)));

        $this->assertFalse($point->equals(new Point(1.000000001, 2.000000001, 3, 4)));
        $this->assertFalse($point->equals(new Point(0.999999999, 1.999999999, 3, 4)));

        $this->assertFalse($point->equals(new GeometryCollection()));
    }

    /**
     * @throws Exception
     */
    public function testFlatten(): void
    {
        $point = new Point(1, 2, 3, 4);
        $point->flatten();

        $this->assertEquals(1, $point->x());
        $this->assertEquals(2, $point->y());
        $this->assertNull($point->z());
        $this->assertNull($point->m());
        $this->assertFalse($point->is3D());
        $this->assertFalse($point->isMeasured());
    }

    /**
     * @throws Exception
     */
    public static function providerDistance(): array
    {
        return [
            'empty Point' =>
                [new Point(), null],
            'Point x+10' =>
                [new Point(10, 0), 10.0],
            'Point y+10' =>
                [new Point(0, 10), 10.0],
            'Point x+10,y+10' =>
                [new Point(10, 10), 14.142135623730951],
            'LineString, point is a vertex' =>
                [LineString::fromArray([[-10, 10], [0, 0], [10, 10]]), 0.0],
            'LineString, containing a vertex twice' =>
                [LineString::fromArray([[0, 10], [0, 10]]), 10.0],
            'LineString, point on line' =>
                [LineString::fromArray([[-10, -10], [10, 10]]), 0.0],

            'MultiPoint, closest distance is 0' =>
                [MultiPoint::fromArray([[0, 0], [10, 20]]), 0.0],
            'MultiPoint, closest distance is 10' =>

                [MultiPoint::fromArray([[10, 20], [0, 10]]), 10.0],
            'MultiPoint, one of two is empty' => [MultiPoint::fromArray([[], [0, 10]]), 10.0],

            'GeometryCollection, closest component is 10' =>
                [new GeometryCollection([new Point(0,10), new Point()]), 10.0]
            // FIXME: this geometry collection crashes GEOS
            // TODO: test other types
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerDistance')]
    public function testDistance(Geometry $otherGeometry, float|null $expectedDistance): void
    {
        $point = new Point(0, 0);

        $this->assertSame($point->distance($otherGeometry), $expectedDistance);
    }

    #[DataProvider('providerDistance')]
    public function testDistanceEmpty(Geometry $otherGeometry): void
    {
        $point = new Point();

        $this->assertNull($point->distance($otherGeometry));
    }

    /**
     * @throws Exception
     */
    public function testTrivialMethods(): void
    {
        $point = new Point(1, 2, 3, 4);

        $this->assertSame( $point->dimension(), 0 );

        $this->assertSame( $point->numPoints(), 1 );

        $this->assertSame( $point->getPoints(), [$point] );

        $this->assertTrue( $point->isSimple());
    }

    /**
     * @throws Exception
     */
    public function testMinMaxMethods(): void
    {
        $point = new Point(1, 2, 3, 4);

        $this->assertEquals(3, $point->minimumZ());
        $this->assertEquals(3, $point->maximumZ());
        $this->assertEquals(4, $point->minimumM());
        $this->assertEquals(4, $point->maximumM());
    }

    public static function providerMethodsNotValidForPointReturnsNull(): array
    {
        return [
                ['zDifference'],
                ['elevationGain'],
                ['elevationLoss'],
                ['numGeometries'],
                ['geometryN'],
                ['startPoint'],
                ['endPoint'],
                ['isRing'],
                ['isClosed'],
                ['pointN'],
                ['exteriorRing'],
                ['numInteriorRings'],
                ['interiorRingN'],
                ['explode']
        ];
    }

    /**
     * @param string $methodName
     * @throws Exception
     */
    #[DataProvider('providerMethodsNotValidForPointReturnsNull')]
    public function testPlaceholderMethodsReturnsNull(string $methodName): void
    {
        $this->assertNull( (new Point(1, 2, 3, 4))->$methodName(null) );
    }

    public static function providerMethodsNotValidForPointReturns0(): array
    {
        return [
            ['area'],
            ['length'],
            ['length3D'],
            ['greatCircleLength'],
            ['haversineLength']
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('providerMethodsNotValidForPointReturns0')]
    public function testPlaceholderMethods(string $methodName): void
    {
        $this->assertSame( (new Point(1, 2, 3, 4))->$methodName(null), 0.0 );
    }

}
