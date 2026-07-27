<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ServerFaceVerificationService;

class ServerFaceVerificationTest extends TestCase
{
    /**
     * Test face descriptor comparison
     */
    public function test_face_descriptor_comparison()
    {
        // Create two identical descriptors
        $descriptor1 = array_fill(0, 128, 0.5);
        $descriptor2 = array_fill(0, 128, 0.5);

        $result = ServerFaceVerificationService::compareFaceDescriptors($descriptor1, $descriptor2);

        $this->assertTrue($result['match']);
        $this->assertLessThan(0.1, $result['distance']);
    }

    /**
     * Test face descriptor comparison with different faces
     */
    public function test_face_descriptor_mismatch()
    {
        $descriptor1 = array_fill(0, 128, 0.5);
        $descriptor2 = array_fill(0, 128, 0.0);

        $result = ServerFaceVerificationService::compareFaceDescriptors($descriptor1, $descriptor2);

        $this->assertFalse($result['match']);
        $this->assertGreaterThan(0.6, $result['distance']);
    }

    /**
     * Test haversine distance calculation
     * Jakarta (HQ) to nearby location (50m away)
     */
    public function test_haversine_distance_calculation()
    {
        // This test demonstrates that the service can calculate distances
        // Using reflection to test private method
        $reflection = new \ReflectionClass(ServerFaceVerificationService::class);
        $method = $reflection->getMethod('haversineDistance');
        $method->setAccessible(true);

        // Two identical coordinates should return 0
        $distance = $method->invoke(null, -6.2088, 106.8057, -6.2088, 106.8057);
        $this->assertEquals(0, $distance, 'Distance between same coordinates should be 0');

        // Approximate 50m difference
        $lat2 = -6.2088 + (50 / 111000); // Rough conversion: 1 degree ≈ 111km
        $distance = $method->invoke(null, -6.2088, 106.8057, $lat2, 106.8057);
        $this->assertLessThan(100, $distance); // Should be around 50m
        $this->assertGreaterThan(0, $distance);
    }

    /**
     * Test empty descriptor handling
     */
    public function test_empty_descriptor_handling()
    {
        $result = ServerFaceVerificationService::compareFaceDescriptors([], []);

        $this->assertFalse($result['match']);
        $this->assertEquals(1.0, $result['distance']);
    }

    /**
     * Test office location retrieval
     */
    public function test_get_office_location()
    {
        $location = ServerFaceVerificationService::getOfficeLocation();

        $this->assertArrayHasKey('latitude', $location);
        $this->assertArrayHasKey('longitude', $location);
        $this->assertIsFloat($location['latitude']);
        $this->assertIsFloat($location['longitude']);
    }
}
