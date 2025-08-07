// Geolocation Utilities
class GeolocationUtils {
    constructor() {
        this.watchId = null;
        this.lastKnownPosition = null;
    }

    // Get current position with high accuracy
    getCurrentPosition(options = {}) {
        const defaultOptions = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
        };
        
        const finalOptions = { ...defaultOptions, ...options };
        
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation is not supported'));
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.lastKnownPosition = position;
                    resolve(position);
                },
                (error) => {
                    reject(this.getGeolocationError(error));
                },
                finalOptions
            );
        });
    }

    // Watch position changes
    watchPosition(callback, errorCallback, options = {}) {
        const defaultOptions = {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 60000
        };
        
        const finalOptions = { ...defaultOptions, ...options };
        
        if (!navigator.geolocation) {
            errorCallback(new Error('Geolocation is not supported'));
            return null;
        }
        
        this.watchId = navigator.geolocation.watchPosition(
            (position) => {
                this.lastKnownPosition = position;
                callback(position);
            },
            (error) => {
                errorCallback(this.getGeolocationError(error));
            },
            finalOptions
        );
        
        return this.watchId;
    }

    // Stop watching position
    clearWatch() {
        if (this.watchId !== null) {
            navigator.geolocation.clearWatch(this.watchId);
            this.watchId = null;
        }
    }

    // Calculate distance between two points (Haversine formula)
    calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000; // Earth's radius in meters
        const dLat = this.toRadians(lat2 - lat1);
        const dLon = this.toRadians(lon2 - lon1);
        
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(this.toRadians(lat1)) * Math.cos(this.toRadians(lat2)) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
        
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        
        return R * c; // Distance in meters
    }

    // Convert degrees to radians
    toRadians(degrees) {
        return degrees * (Math.PI / 180);
    }

    // Get human-readable error message
    getGeolocationError(error) {
        switch(error.code) {
            case error.PERMISSION_DENIED:
                return new Error("Akses lokasi ditolak oleh pengguna");
            case error.POSITION_UNAVAILABLE:
                return new Error("Informasi lokasi tidak tersedia");
            case error.TIMEOUT:
                return new Error("Permintaan lokasi timeout");
            default:
                return new Error("Error tidak diketahui: " + error.message);
        }
    }

    // Check if user is within radius
    isWithinRadius(userLat, userLon, targetLat, targetLon, radiusMeters) {
        const distance = this.calculateDistance(userLat, userLon, targetLat, targetLon);
        return {
            withinRadius: distance <= radiusMeters,
            distance: distance,
            radiusMeters: radiusMeters
        };
    }

    // Get location accuracy level
    getAccuracyLevel(accuracy) {
        if (accuracy <= 5) return 'Sangat Tinggi';
        if (accuracy <= 10) return 'Tinggi';
        if (accuracy <= 20) return 'Sedang';
        if (accuracy <= 50) return 'Rendah';
        return 'Sangat Rendah';
    }

    // Format coordinates for display
    formatCoordinates(lat, lon, precision = 6) {
        return {
            latitude: parseFloat(lat).toFixed(precision),
            longitude: parseFloat(lon).toFixed(precision),
            dms: this.toDMS(lat, lon)
        };
    }

    // Convert decimal degrees to DMS (Degrees, Minutes, Seconds)
    toDMS(lat, lon) {
        const latDMS = this.convertToDMS(lat, ['N', 'S']);
        const lonDMS = this.convertToDMS(lon, ['E', 'W']);
        return `${latDMS}, ${lonDMS}`;
    }

    convertToDMS(coordinate, directions) {
        const absolute = Math.abs(coordinate);
        const degrees = Math.floor(absolute);
        const minutesFloat = (absolute - degrees) * 60;
        const minutes = Math.floor(minutesFloat);
        const seconds = ((minutesFloat - minutes) * 60).toFixed(2);
        const direction = directions[coordinate >= 0 ? 0 : 1];
        
        return `${degrees}°${minutes}'${seconds}"${direction}`;
    }
}

// Export for use
window.GeolocationUtils = GeolocationUtils;
