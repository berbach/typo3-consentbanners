// fingerprint-generator.js
/**
 * @class BrowserFingerprint
 * @description Generates a basic browser fingerprint based on various browser and device properties.
 * Intended for documenting cookie consent decisions, not for pervasive tracking.
 */
class BrowserFingerprint {
    constructor() {
        this.data = {};
    }

    /**
     * @private
     * @returns {string} The user agent string.
     */
    _getUserAgent() {
        return navigator.userAgent || 'unknown';
    }

    /**
     * @private
     * @returns {string} The screen resolution in 'WxH' format.
     */
    _getScreenResolution() {
        return `${screen.width}x${screen.height}`;
    }

    /**
     * @private
     * @returns {number} The color depth of the screen.
     */
    _getColorDepth() {
        return screen.colorDepth || 'unknown';
    }

    /**
     * @private
     * @returns {string} The current time zone of the user's system.
     */
    _getTimeZone() {
        try {
            return Intl.DateTimeFormat().resolvedOptions().timeZone;
        } catch (e) {
            return 'unknown';
        }
    }

    /**
     * @private
     * @returns {string} A hash generated from a canvas rendering.
     * This is a strong fingerprinting component.
     */
    _getCanvasFingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.width = 200;
            canvas.height = 20;
            ctx.textBaseline = "top";
            ctx.font = "14px 'Arial'";
            ctx.textBaseline = "alphabetic";
            ctx.fillStyle = "#f60";
            ctx.fillRect(125, 1, 62, 20);
            ctx.fillStyle = "#069";
            ctx.fillText("FP Test", 2, 15); // Use a simple, consistent text
            return canvas.toDataURL(); // Returns base64 encoded image data
        } catch (e) {
            return 'canvas_error';
        }
    }

    /**
     * @private
     * @returns {string} A hash generated from WebGL renderer information.
     * Another strong fingerprinting component.
     */
    _getWebGLFingerprint() {
        try {
            const canvas = document.createElement('canvas');
            const gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
            if (gl) {
                const debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                const vendor = gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL) || 'unknown';
                const renderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) || 'unknown';
                return `${vendor}|${renderer}`;
            }
            return 'webgl_not_supported';
        } catch (e) {
            return 'webgl_error';
        }
    }

    /**
     * Gathers all fingerprint components.
     * @returns {object} An object containing all collected fingerprint data.
     */
    async collect() {
        this.data = {
            userAgent: this._getUserAgent(),
            screenResolution: this._getScreenResolution(),
            colorDepth: this._getColorDepth(),
            timeZone: this._getTimeZone(),
            canvasHash: this._getCanvasFingerprint(),
            webGLInfo: this._getWebGLFingerprint(),
            // Add more components if needed, e.g., AudioContext fingerprinting (more complex)
            // hardwareConcurrency: navigator.hardwareConcurrency || 'unknown',
            // languages: navigator.languages.join(',') || 'unknown',
            // platform: navigator.platform || 'unknown',
        };
        return this.data;
    }

    /**
     * Generates a unique hash from the collected fingerprint data.
     * @returns {string} The SHA-256 hash of the fingerprint data.
     */
    async generateHash() {
        await this.collect(); // Ensure data is collected before hashing

        const fingerprintString = JSON.stringify(this.data);
        const textEncoder = new TextEncoder();
        const data = textEncoder.encode(fingerprintString);

        // Use Web Crypto API for a strong, cryptographic hash (SHA-256)
        const hashBuffer = await crypto.subtle.digest('SHA-256', data);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hexHash = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');

        return hexHash;
    }
}

export default BrowserFingerprint;