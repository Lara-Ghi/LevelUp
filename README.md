# LevelUp

LevelUp is a Group 3 web app that guides users through healthy sit–stand cycles using Cornell ergonomics research, awards points that can be redeemed for gift cards, and can optionally integrate hardware features like LED alerts and display text on an OLED display via a Raspberry Pi Pico W.

## Highlights

- Sit/stand scheduler with audible and visual cues
- Wifi2ble simulator that mimics a LINAK desk
- Daily point tracking with anti-gaming rules and feedback colors
- Optional Rasberry Pi Pico W board integration (OLED + RGB LED status display)

## Wifi2ble Simulator

The wifi2ble box simulator exposes the same API as a LINAK desk controller so you can test commands locally without moving a real desk. Run it when you want to validate commands, telemetry, and logging.

## Points At A Glance

- **Daily cap**: Earn up to 160 points per day
- **Minimum cycle**: 15 minutes total, shorter cycles earn 0 points
- **Scoring**: Weighted blend of ratio accuracy (70%, target 20 min sit : 10 min stand) and duration balance (30%, target ~30 min)

| Health Score | Points | Feedback |
| --- | --- | --- |
| 90–100 | +10 | 🟢 Perfect balance |
| 70–89 | +7 | 🟡 Great — minor tweaks optional |
| 50–69 | +4 | 🟠 Fair — adjust times |
| <50 or <15 min | 0 | 🔴 Too short or imbalanced |

## Run It

Full environment instructions (local PHP, Docker stack, simulator ports, Pico W hardware flashing, real-time log tailing, and common troubleshooting) now live in `SETUP.md`. Start there for step-by-step commands and the log command that streams desk-simulator traffic while you test.

## Pico W Controls

- **OLED display** – Shows the active user greeting, total points, and real-time sit/stand alerts so you can demo LevelUp away from the browser.
- **RGB LED** – Glows purple while sitting, green while standing, and dims or pulses when the timer is paused.
- **Potentiometer** – Connected to the Pico’s ADC to modulate LED brightness; turn the knob to match the lighting of your workspace or make demos camera-friendly.
- **Pause button (GP10)** – Mirrors the in-app pause/resume toggle so you can control the timer from the hardware without touching the UI.

See `SETUP.md` for flashing instructions via Thonny.
