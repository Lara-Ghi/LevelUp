"""
LevelUp Pico W Display Client
Connects to phone hotspot, fetches current user from Laravel backend,
and displays personalized greeting on OLED screen.
"""

import network # type: ignore
import urequests # type: ignore
import json
import time
from machine import Pin, SoftI2C, ADC # type: ignore
from ssd1306 import SSD1306_I2C
from config import (
    WIFI_SSID, WIFI_PASSWORD, API_URL,
    POLL_INTERVAL_SECONDS, API_TIMEOUT_SECONDS, DEFAULT_MESSAGE,
    I2C_SDA_PIN, I2C_SCL_PIN, I2C_FREQ,
    OLED_WIDTH, OLED_HEIGHT, OLED_ADDR,
    RGB_LED_PIN, RGB_LED_COUNT,
    SITTING_COLOR, STANDING_COLOR, OFF_COLOR,
    POT_PIN, BRIGHTNESS_MIN
)

# Try to import neopixel (might not be available on all boards)
try:
    import neopixel # type: ignore
    NEOPIXEL_AVAILABLE = True
except ImportError:
    NEOPIXEL_AVAILABLE = False
    print("Warning: neopixel module not available")

# Cache file for offline fallback
CACHE_FILE = "user_cache.json"

# Global variables
oled = None
last_displayed_message = None
wlan = None
rgb_led = None
potentiometer = None
last_led_phase = None
current_led_color = OFF_COLOR  # Track current LED base color
last_brightness = None  # Track last brightness value
in_warning_mode = False  # Track if we're in warning mode


def log(message):
    """Print timestamped log message"""
    print(f"[{time.time()}] {message}")


def connect_wifi():
    """Connect to WiFi hotspot"""
    global wlan
    
    log(f"Connecting to WiFi: {WIFI_SSID}")
    wlan = network.WLAN(network.STA_IF)
    wlan.active(True)
    
    if wlan.isconnected():
        log("Already connected to WiFi")
        return True
    
    # Start connection
    wlan.connect(WIFI_SSID, WIFI_PASSWORD)
    
    # Wait for connection
    max_wait = 30
    while max_wait > 0:
        status = wlan.status()
        
        if status == 3:  # Connected
            ip = wlan.ifconfig()[0]
            log(f"WiFi connected! IP: {ip}")
            return True
        elif status < 0:  # Error
            log(f"WiFi connection failed with status: {status}")
            return False
        
        max_wait -= 1
        time.sleep(1)
    
    log("WiFi connection timeout")
    return False


def check_wifi():
    """Check if WiFi is still connected, reconnect if needed"""
    global wlan
    
    if not wlan or not wlan.isconnected():
        log("WiFi disconnected, reconnecting...")
        return connect_wifi()
    
    return True


def load_cached_user():
    """Load user data from cache file"""
    try:
        with open(CACHE_FILE, "r") as f:
            data = json.load(f)
            log(f"Loaded cached user: {data.get('username', 'Unknown')}")
            return data
    except Exception as e:
        log(f"Failed to load cache: {e}")
        return None


def save_cached_user(data):
    """Save user data to cache file"""
    try:
        with open(CACHE_FILE, "w") as f:
            json.dump(data, f)
        log("User data cached successfully")
    except Exception as e:
        log(f"Failed to save cache: {e}")


def fetch_user_data():
    """Fetch current user data from Laravel API"""
    try:
        log(f"Fetching user data from {API_URL}")
        response = urequests.get(API_URL, timeout=API_TIMEOUT_SECONDS)
        
        if response.status_code == 200:
            data = response.json()
            log(f"API response: {data}")
            
            # Save to cache for offline fallback
            save_cached_user(data)
            
            response.close()
            return data
        else:
            log(f"API error: HTTP {response.status_code}")
            response.close()
            return None
            
    except OSError as e:
        log(f"Network error: {e} - Check WiFi connection")
        return None
    except Exception as e:
        log(f"API request failed: {e}")
        return None


def get_display_data():
    """Get user data from API or cache"""
    
    # Try to fetch from API first
    if check_wifi():
        data = fetch_user_data()
        if data:
            return data
    
    # Fallback to cache
    log("Using cached data...")
    cached = load_cached_user()
    if cached:
        return cached
    
    # Ultimate fallback
    return {
        'message': DEFAULT_MESSAGE,
        'username': None,
        'logged_in': False
    }


def wrap_text(text, width=16):
    """
    Break text into lines that fit the OLED display
    width: characters per line (approximate, depends on font)
    """
    words = text.split()
    lines = []
    current_line = ""
    
    for word in words:
        test_line = f"{current_line} {word}".strip()
        if len(test_line) <= width:
            current_line = test_line
        else:
            if current_line:
                lines.append(current_line)
            current_line = word
    
    if current_line:
        lines.append(current_line)
    
    return lines if lines else [text[:width]]


def center_text(text, width=16):
    """Center text within the given width"""
    if len(text) >= width:
        return 0
    return (width * 8 - len(text) * 8) // 2  # 8 pixels per character


def init_rgb_led():
    """Initialize RGB LED (WS2812) on GP6"""
    global rgb_led
    
    if not NEOPIXEL_AVAILABLE:
        log("Neopixel module not available, RGB LED disabled")
        return False
    
    try:
        log(f"Initializing RGB LED on GP{RGB_LED_PIN}...")
        rgb_led = neopixel.NeoPixel(Pin(RGB_LED_PIN, Pin.OUT), RGB_LED_COUNT)
        rgb_led[0] = OFF_COLOR
        rgb_led.write()
        log("RGB LED initialized")
        return True
    except Exception as e:
        log(f"Failed to initialize RGB LED: {e}")
        return False


def init_potentiometer():
    """Initialize potentiometer for brightness control"""
    global potentiometer
    
    try:
        log(f"Initializing potentiometer on GP{POT_PIN}...")
        potentiometer = ADC(POT_PIN)
        log("Potentiometer initialized")
        return True
    except Exception as e:
        log(f"Failed to initialize potentiometer: {e}")
        return False


def read_brightness():
    """Read brightness level from potentiometer (0.0 to 1.0)"""
    if not potentiometer:
        return 1.0  # Full brightness if no potentiometer
    
    try:
        # Read ADC value (0-65535)
        raw_value = potentiometer.read_u16()
        # Convert to 0.0-1.0 range
        brightness = raw_value / 65535.0
        # Apply minimum brightness
        brightness = max(BRIGHTNESS_MIN, brightness)
        return brightness
    except Exception as e:
        log(f"Error reading potentiometer: {e}")
        return 1.0


def apply_brightness(color, brightness):
    """Apply brightness level to RGB color"""
    return tuple(int(c * brightness) for c in color)


def update_rgb_led(phase, time_remaining=None):
    """Update RGB LED based on timer phase"""
    global last_led_phase, current_led_color, in_warning_mode
    
    log(f"update_rgb_led called with phase: {phase}, time_remaining: {time_remaining}, last_phase: {last_led_phase}")
    
    if not rgb_led:
        log("RGB LED not initialized, skipping update")
        return
    
    # Determine base color based on phase and time remaining
    base_color = None
    
    # Check if we're in warning mode (≤30 seconds)
    is_warning = time_remaining is not None and time_remaining <= 30 and time_remaining > 0 and phase
    
    # Show action color if in warning mode
    if is_warning:
        in_warning_mode = True
        if phase == 'sitting':
            # "Get ready to stand up!" - show GREEN (standing color)
            base_color = STANDING_COLOR
            log(f"WARNING: {time_remaining}s remaining - Get ready to STAND UP (green)")
        elif phase == 'standing':
            # "Get ready to sit down!" - show PURPLE (sitting color)
            base_color = SITTING_COLOR
            log(f"WARNING: {time_remaining}s remaining - Get ready to SIT DOWN (purple)")
        
        current_led_color = base_color
        last_led_phase = 'warning_' + phase
    elif phase != last_led_phase or (in_warning_mode and not is_warning):
        # Normal phase change or exiting warning mode
        in_warning_mode = False
        if phase == 'sitting':
            base_color = SITTING_COLOR
            log(f"Setting LED to SITTING (purple)")
        elif phase == 'standing':
            base_color = STANDING_COLOR
            log(f"Setting LED to STANDING (green)")
        else:
            base_color = OFF_COLOR
            log(f"Setting LED to OFF (phase={phase})")
        
        current_led_color = base_color
        last_led_phase = phase
    
    # Apply brightness and update LED if color changed
    if base_color is not None:
        brightness = read_brightness()
        color = apply_brightness(current_led_color, brightness)
        rgb_led[0] = color
        rgb_led.write()
        log(f"LED updated - brightness: {brightness:.2f}, color: {color}")


def update_led_brightness():
    """Update LED brightness based on potentiometer (called frequently)"""
    global last_brightness
    
    if not rgb_led or current_led_color == OFF_COLOR:
        return
    
    brightness = read_brightness()
    
    # Only update if brightness changed significantly (avoid flicker)
    if last_brightness is None or abs(brightness - last_brightness) > 0.05:
        color = apply_brightness(current_led_color, brightness)
        rgb_led[0] = color
        rgb_led.write()
        last_brightness = brightness


def display_message(data):
    """Display message on OLED screen"""
    global last_displayed_message, in_warning_mode
    
    if not oled:
        log("OLED not initialized")
        return
    
    # Extract data
    name = data.get('name')
    logged_in = data.get('logged_in', False)
    timer_phase = data.get('timer_phase')
    time_remaining = data.get('time_remaining')
    warning_message = data.get('warning_message')
    
    # Update RGB LED based on timer phase and time remaining
    update_rgb_led(timer_phase, time_remaining)
    
    # Determine what message to display
    # Priority: warning_message > greeting (but keep warning if still in warning mode)
    if warning_message or (in_warning_mode and time_remaining is not None and time_remaining <= 30):
        # Show warning message if backend provides it OR if we're still in warning mode
        if warning_message:
            display_text = warning_message
        else:
            # Generate warning message locally if backend hasn't sent it yet
            if timer_phase == 'sitting':
                display_text = 'Get ready to stand up!'
            else:
                display_text = 'Get ready to sit down!'
    elif name and logged_in:
        # Show personalized greeting
        display_text = f"Hello, {name}!"
    else:
        # Show default message
        display_text = data.get('message', DEFAULT_MESSAGE)
    
    # Don't update if message hasn't changed
    if display_text == last_displayed_message:
        return
    
    log(f"Displaying: {display_text}")
    
    try:
        # Clear display
        oled.fill(0)
        
        # Special handling for "Welcome to LevelUp!" - split into 2 centered lines
        if display_text == "Welcome to LevelUp!":
            line1 = "Welcome to"
            line2 = "LevelUp!"
            
            # Center both lines
            x1 = center_text(line1, 16)
            x2 = center_text(line2, 16)
            
            if OLED_HEIGHT == 32:
                oled.text(line1, x1, 8)
                oled.text(line2, x2, 20)
            else:
                oled.text(line1, x1, 24)
                oled.text(line2, x2, 36)
        else:
            # Wrap text to fit display
            lines = wrap_text(display_text, width=16)
            
            # Limit to 3 lines for 32px height (or 7 lines for 64px)
            max_lines = 3 if OLED_HEIGHT == 32 else 7
            lines = lines[:max_lines]
            
            # Calculate vertical centering
            line_height = 10
            total_height = len(lines) * line_height
            start_y = max(0, (OLED_HEIGHT - total_height) // 2)
            
            # Display each line (centered)
            for i, line in enumerate(lines):
                y_pos = start_y + (i * line_height)
                x_pos = center_text(line, 16)
                oled.text(line, x_pos, y_pos)
        
        # Update display
        oled.show()
        
        last_displayed_message = display_text
        log("Display updated successfully")
        
    except Exception as e:
        log(f"Display error: {e}")


def init_display():
    """Initialize I2C OLED display"""
    global oled
    
    try:
        log("Initializing I2C OLED display...")
        
        # Setup I2C
        sda = Pin(I2C_SDA_PIN)
        scl = Pin(I2C_SCL_PIN)
        i2c = SoftI2C(sda=sda, scl=scl, freq=I2C_FREQ)
        
        # Scan for devices
        devices = i2c.scan()
        log(f"I2C devices found: {[hex(d) for d in devices]}")
        
        if OLED_ADDR not in devices:
            log(f"Warning: OLED not found at address {hex(OLED_ADDR)}")
        
        # Initialize OLED
        oled = SSD1306_I2C(OLED_WIDTH, OLED_HEIGHT, i2c, addr=OLED_ADDR)
        log(f"OLED initialized: {OLED_WIDTH}x{OLED_HEIGHT}")
        
        # Test display (centered)
        oled.fill(0)
        test_text = "LevelUp!"
        x_pos = center_text(test_text, 16)
        oled.text(test_text, x_pos, 12)
        oled.show()
        time.sleep(1)
        
        return True
        
    except Exception as e:
        log(f"Failed to initialize display: {e}")
        return False


def main():
    """Main program loop"""
    log("=== LevelUp Pico W Starting ===")
    
    # Initialize display first
    if not init_display():
        log("Cannot continue without display")
        return
    
    # Initialize RGB LED and potentiometer
    init_rgb_led()
    init_potentiometer()
    
    # Show connecting message (centered)
    oled.fill(0)
    connecting_text = "Connecting..."
    x_pos = center_text(connecting_text, 16)
    oled.text(connecting_text, x_pos, 12)
    oled.show()
    
    # Connect to WiFi
    if not connect_wifi():
        log("Cannot connect to WiFi")
        oled.fill(0)
        oled.text("WiFi Failed!", 15, 8)
        oled.text("Check config", 10, 20)
        oled.show()
        return
    
    # Show ready message (centered)
    oled.fill(0)
    ready_text = "Connected!"
    x_pos = center_text(ready_text, 16)
    oled.text(ready_text, x_pos, 12)
    oled.show()
    time.sleep(1)
    
    log("Starting main loop...")
    
    # Main loop
    retry_count = 0
    max_retries = 3
    last_data_fetch = 0
    
    while True:
        try:
            # Update LED brightness from potentiometer (fast, non-blocking)
            update_led_brightness()
            
            # Fetch data at regular intervals
            current_time = time.time()
            if current_time - last_data_fetch >= POLL_INTERVAL_SECONDS:
                # Get and display user data
                data = get_display_data()
                display_message(data)
                last_data_fetch = current_time
                
                # Reset retry counter on success
                retry_count = 0
            
            # Small delay to avoid CPU overload but keep brightness responsive
            time.sleep(0.1)
            
        except Exception as e:
            log(f"Loop error: {e}")
            retry_count += 1
            
            if retry_count >= max_retries:
                log("Too many errors, displaying fallback message")
                oled.fill(0)
                error_text = "System Error"
                restart_text = "Restarting..."
                x_pos1 = center_text(error_text, 16)
                x_pos2 = center_text(restart_text, 16)
                oled.text(error_text, x_pos1, 8)
                oled.text(restart_text, x_pos2, 20)
                oled.show()
                time.sleep(5)
                retry_count = 0
            
            time.sleep(2)


# Entry point
if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        log("Program stopped by user")
        if oled:
            oled.fill(0)
            stopped_text = "Stopped"
            x_pos = center_text(stopped_text, 16)
            oled.text(stopped_text, x_pos, 12)
            oled.show()
    except Exception as e:
        log(f"Fatal error: {e}")
        if oled:
            oled.fill(0)
            error_text = "Fatal Error"
            x_pos = center_text(error_text, 16)
            oled.text(error_text, x_pos, 12)
            oled.show()
