"""
Configuration file for Pico W connection to LevelUp Laravel backend
"""

# WiFi credentials for your phone hotspot
WIFI_SSID = "your_phone"
WIFI_PASSWORD = "your_password"

# Laravel API endpoint
# Update this IP to match your laptop's IP on the hotspot network
# Run ipconfig (Windows) and use the IPv4 address for "Wireless LAN adapter Wi-Fi"
API_URL = "http://your_ip/api/pico/display"

# Display settings
POLL_INTERVAL_SECONDS = 2  # How often to fetch data from API (2 seconds = faster response)
API_TIMEOUT_SECONDS = 3     # Timeout for API requests
DEFAULT_MESSAGE = "Welcome to LevelUp!"

# I2C pins for OLED display (SSD1306)
I2C_SDA_PIN = 4  # GPIO 4
I2C_SCL_PIN = 5  # GPIO 5
I2C_FREQ = 100000  # 100kHz

# OLED display settings
OLED_WIDTH = 128
OLED_HEIGHT = 32  # Try 32 first, if it doesn't work, change to 64
OLED_ADDR = 0x3C  # I2C address

# RGB LED settings (WS2812)
RGB_LED_PIN = 6  # GPIO 6
RGB_LED_COUNT = 1  # Number of LEDs
SITTING_COLOR = (128, 0, 128)  # Purple for sitting phase
STANDING_COLOR = (0, 128, 0)  # Green for standing phase
OFF_COLOR = (0, 0, 0)  # LED off

# Potentiometer settings (for brightness control)
POT_PIN = 26  # GPIO 26 (ADC0)
BRIGHTNESS_MIN = 0.0  # Minimum brightness (0.0 = off, 1.0 = full)

# Button and pause LED settings
PAUSE_BUTTON_PIN = 10  # GPIO 10 (pause/resume button)
PAUSE_LED_PIN = 7      # GPIO 7 (pause indicator LED)
