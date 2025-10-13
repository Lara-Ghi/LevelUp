// ===========================================
// ⏰ FOCUS CLOCK CORE - TIMER LOGIC
// ===========================================

class FocusClockCore {
    constructor() {
        this.sittingTime = 20; // minutes (LINAK 20:10 pattern)
        this.standingTime = 10; // minutes (LINAK 20:10 pattern)
        this.currentTime = 0; // seconds
        this.isRunning = false;
        this.isSittingSession = true;
        this.cycleCount = 0;
        this.intervalId = null;
        this.sessionStartTime = null; // When the current session started
        this.sessionDuration = 0; // Total duration of current session in seconds
        this.callbacks = {
            onTick: () => {},
            onSessionChange: () => {},
            onCycleComplete: () => {}
        };
    }

    // Initialize with custom sitting and standing times
    initialize(sittingMinutes, standingMinutes) {
        this.sittingTime = Math.max(1, sittingMinutes);
        this.standingTime = Math.max(1, standingMinutes);
        this.currentTime = this.sittingTime * 60; // Convert to seconds
        this.sessionDuration = this.sittingTime * 60;
        this.isSittingSession = true;
        this.cycleCount = 0;
    }

    // Set callback functions
    setCallbacks(callbacks) {
        this.callbacks = { ...this.callbacks, ...callbacks };
    }

    // Start the timer
    start() {
        if (this.isRunning) return;

        this.isRunning = true;
        this.sessionStartTime = Date.now();
        this.sessionDuration = this.isSittingSession ? this.sittingTime * 60 : this.standingTime * 60;

        this.intervalId = setInterval(() => {
            this.tick();
        }, 100); // More frequent updates for better accuracy
    }

    // Pause the timer
    pause() {
        this.isRunning = false;
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
        // Calculate remaining time when paused
        if (this.sessionStartTime) {
            const elapsed = Math.floor((Date.now() - this.sessionStartTime) / 1000);
            this.currentTime = Math.max(0, this.sessionDuration - elapsed);
        }
    }

    // Stop and reset the timer
    stop() {
        this.pause();
        this.currentTime = this.isSittingSession ? this.sittingTime * 60 : this.standingTime * 60;
        this.sessionDuration = this.currentTime;
        this.sessionStartTime = null;
        this.callbacks.onTick(this.currentTime, this.isSittingSession);
    }

    // Timer tick function - always calculates based on session start time
    tick() {
        if (!this.isRunning || !this.sessionStartTime) return;

        // Calculate elapsed time since session started
        const elapsedSeconds = Math.floor((Date.now() - this.sessionStartTime) / 1000);
        this.currentTime = Math.max(0, this.sessionDuration - elapsedSeconds);

        if (this.currentTime <= 0) {
            // Ensure we don't get stuck in a loop
            this.currentTime = 0;
            this.completeSession();
            return;
        }

        this.callbacks.onTick(this.currentTime, this.isSittingSession);
    }

    // Complete current session and switch
    completeSession() {
        console.log(`🔄 Completing session - Current state: ${this.isSittingSession ? 'Sitting' : 'Standing'}`);
        console.log(`Current time left: ${this.currentTime}s, Session duration: ${this.sessionDuration}s`);
        
        // Validate timer settings before proceeding
        if (this.sittingTime <= 0 || this.standingTime <= 0) {
            console.error('❌ Invalid timer settings detected!', {
                sitting: this.sittingTime,
                standing: this.standingTime
            });
            return;
        }
        
        const previousSession = this.isSittingSession;
        
        if (this.isSittingSession) {
            // Sitting session completed, switch to standing
            console.log('✅ Switching from sitting to standing');
            this.isSittingSession = false;
            this.currentTime = this.standingTime * 60;
            this.sessionDuration = this.standingTime * 60;
            this.cycleCount++;
            
            console.log(`🔢 Cycle count updated to: ${this.cycleCount}`);
            this.callbacks.onCycleComplete(this.cycleCount);
        } else {
            // Standing session completed, switch to sitting
            console.log('✅ Switching from standing to sitting');
            this.isSittingSession = true;
            this.currentTime = this.sittingTime * 60;
            this.sessionDuration = this.sittingTime * 60;
        }

        console.log(`🔄 Session switched: ${previousSession ? 'Sitting' : 'Standing'} → ${this.isSittingSession ? 'Sitting' : 'Standing'}`);
        console.log(`⏰ New session - Duration: ${this.sessionDuration}s, Current time: ${this.currentTime}s`);
        
        // Call session change callback with the NEW session state
        this.callbacks.onSessionChange(this.isSittingSession);

        // Update display immediately with new session values
        this.callbacks.onTick(this.currentTime, this.isSittingSession);

        // Continue running if it was running - reset session start time for new session
        if (this.isRunning) {
            this.sessionStartTime = Date.now();
            console.log('⏳ Timer continues running with new session start time');
        }
    }

    // Get current session info
    getCurrentSession() {
        return {
            isSitting: this.isSittingSession,
            timeLeft: this.currentTime,
            cycleCount: this.cycleCount,
            isRunning: this.isRunning
        };
    }

    // Update sitting/standing times
    updateTimes(sittingMinutes, standingMinutes) {
        const wasRunning = this.isRunning;
        this.pause();

        this.sittingTime = Math.max(1, sittingMinutes);
        this.standingTime = Math.max(1, standingMinutes);

        // Reset current time based on current session
        this.currentTime = this.isSittingSession ? this.sittingTime * 60 : this.standingTime * 60;
        this.sessionDuration = this.currentTime;

        if (wasRunning) {
            this.start();
        }

        this.callbacks.onTick(this.currentTime, this.isSittingSession);
    }

    // Format time for display
    static formatTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    // Validate healthy sitting-to-standing ratio
    static validateHealthyRatio(sittingMinutes, standingMinutes) {
        const ratio = sittingMinutes / standingMinutes;
        const idealRatio = 2; // 20min:10min = 2:1 ratio (Cornell University research - LINAK 20:10 pattern)

        let recommendation = '';
        let isHealthy = true;
        let level = 'good';

        if (ratio >= 1.5 && ratio <= 2.5) {
            recommendation = 'Perfect! This follows Cornell University research for optimal desk worker health (20:10 pattern).';
            level = 'good';
        } else if (ratio < 1.5) {
            recommendation = 'Good effort! You might be standing a bit too much. Aim for the 20:10 ratio.';
            level = 'good';
        } else if (ratio <= 4) {
            recommendation = 'Consider more standing breaks. Cornell research shows 20 minutes sitting to 10 standing is ideal.';
            level = 'warning';
            isHealthy = false;
        } else {
            recommendation = 'Health Alert: Too much sitting. Try the 20:10 pattern recommended by Cornell University research.';
            level = 'warning';
            isHealthy = false;
        }

        return {
            isHealthy: isHealthy,
            ratio: ratio,
            idealRatio: idealRatio,
            recommendation: recommendation,
            level: level
        };
    }
}

// Export for use in other files
window.FocusClockCore = FocusClockCore;

// ===========================================
// ⏰ FOCUS CLOCK STORAGE - LOCAL STORAGE MANAGEMENT
// ===========================================

class FocusClockStorage {
    constructor() {
        this.storageKey = 'levelup_focus_clock_settings';
        this.defaultSettings = {
            sittingTime: 20, // LINAK 20:10 pattern
            standingTime: 10, // LINAK 20:10 pattern
            isFirstTime: true,
            totalCycles: 0,
            lastUsed: null
        };
    }

    // Get user settings from localStorage
    getSettings() {
        try {
            const stored = localStorage.getItem(this.storageKey);
            if (stored) {
                const settings = JSON.parse(stored);
                return { ...this.defaultSettings, ...settings };
            }
        } catch (error) {
            console.warn('Error loading Focus Clock settings:', error);
        }
        return { ...this.defaultSettings };
    }

    // Save user settings to localStorage
    saveSettings(settings) {
        try {
            const currentSettings = this.getSettings();
            const updatedSettings = {
                ...currentSettings,
                ...settings,
                sittingTime: Math.max(1, parseInt(settings.sittingTime) || currentSettings.sittingTime),
                standingTime: Math.max(1, parseInt(settings.standingTime) || currentSettings.standingTime),
                lastUsed: new Date().toISOString()
            };
            localStorage.setItem(this.storageKey, JSON.stringify(updatedSettings));
            return true;
        } catch (error) {
            console.error('Error saving Focus Clock settings:', error);
            return false;
        }
    }

    // Mark as not first time
    markAsConfigured() {
        this.saveSettings({ isFirstTime: false });
    }

    // Update sitting and standing times
    updateTimes(sittingTime, standingTime) {
        return this.saveSettings({
            sittingTime: sittingTime,
            standingTime: standingTime
        });
    }

    // Increment cycle count
    incrementCycles() {
        const settings = this.getSettings();
        return this.saveSettings({
            totalCycles: settings.totalCycles + 1
        });
    }

    // Check if user is first time
    isFirstTimeUser() {
        return this.getSettings().isFirstTime;
    }

    // Get total cycles completed
    getTotalCycles() {
        return this.getSettings().totalCycles;
    }

    // Reset all settings (for testing or user reset)
    resetSettings() {
        try {
            localStorage.removeItem(this.storageKey);
            return true;
        } catch (error) {
            console.error('Error resetting Focus Clock settings:', error);
            return false;
        }
    }

    // Export settings for backup
    exportSettings() {
        return this.getSettings();
    }

    // Import settings from backup
    importSettings(settings) {
        if (typeof settings !== 'object' || settings === null) {
            throw new Error('Invalid settings format');
        }

        const validatedSettings = {
            sittingTime: Math.max(1, parseInt(settings.sittingTime) || this.defaultSettings.sittingTime),
            standingTime: Math.max(1, parseInt(settings.standingTime) || this.defaultSettings.standingTime),
            isFirstTime: false,
            totalCycles: Math.max(0, parseInt(settings.totalCycles) || 0)
        };

        return this.saveSettings(validatedSettings);
    }

    // Get usage statistics
    getUsageStats() {
        const settings = this.getSettings();
        const totalSittingTime = settings.totalCycles * settings.sittingTime; // in minutes
        const totalStandingTime = settings.totalCycles * settings.standingTime; // in minutes

        return {
            totalCycles: settings.totalCycles,
            totalSittingTime: totalSittingTime,
            totalStandingTime: totalStandingTime,
            averageSessionLength: settings.sittingTime + settings.standingTime,
            lastUsed: settings.lastUsed
        };
    }
}

// Export for use in other files
window.FocusClockStorage = FocusClockStorage;

// ===========================================
// ⏰ FOCUS CLOCK UI - USER INTERFACE MANAGEMENT
// ===========================================

class FocusClockUI {
    constructor() {
        this.core = new FocusClockCore();
        this.storage = new FocusClockStorage();
        this.elements = {};
        this.isInitialized = false;

        // Alarm system properties
        this.alarmAudio = null;
        this.isAlarmPlaying = false;
        this.alarmTimeout = null;
        this.snoozeTime = 5 * 60; // 5 minutes snooze

        this.init();
    }

    // Initialize the Focus Clock UI
    init() {
        this.createHTML();
        this.bindElements();
        this.setupEventListeners();
        this.setupCoreCallbacks();

        // Check if first time user
        if (this.storage.isFirstTimeUser()) {
            this.showSetupModal();
        } else {
            this.loadSavedSettings();
        }

        this.isInitialized = true;

        // Initialize alarm system
        this.initAlarmSystem();
        
        // Add test methods to window for debugging
        window.testAlarm = () => {
            console.log('🧪 Testing alarm system manually...');
            this.handleSessionChange(false); // Trigger standing session
        };
        
        window.quickTest = () => {
            console.log('🚀 Setting up 10-second sitting, 5-second standing test...');
            this.core.pause();
            this.core.sittingTime = 10/60; // 10 seconds
            this.core.standingTime = 5/60; // 5 seconds
            this.core.currentTime = 10;
            this.core.sessionDuration = 10;
            this.core.isSittingSession = true;
            this.updateDisplay(10, true);
            console.log('✅ Ready! Click Start to test with 10-second sitting, 5-second standing timer');
        };
    }

    // Initialize alarm system
    initAlarmSystem() {
        // Create audio element for alarm
        this.alarmAudio = new Audio('/alarm_files/alarm_1.mp3');
        this.alarmAudio.loop = true; // Keep looping until stopped
        this.alarmAudio.volume = 0.7; // Set reasonable volume
        this.alarmAudio.preload = 'auto'; // Preload the audio file

        // Handle audio loading errors
        this.alarmAudio.addEventListener('error', (e) => {
            console.warn('Alarm audio failed to load:', e);
            this.showAlarmError();
        });

        // Handle successful loading
        this.alarmAudio.addEventListener('canplaythrough', () => {
            console.log('Alarm audio loaded successfully');
        });
    }

    // Create HTML structure
    createHTML() {
        const clockHTML = `
            <!-- Focus Clock Section -->
            <section class="clock-section">
                <div class="clock-container">
                    <div class="clock-header">
                        <h2 class="clock-title">Desk Timer</h2>
                        <div class="clock-cycle-info">
                            <span class="cycle-count">Cycle: <span id="cycleNumber">0</span></span>
                            <span class="clock-stat-inline">
                                <i class="fas fa-chair"></i>
                                <span class="session-type sitting-label">Sitting</span>
                                <span class="stat-value" id="sittingTimeInfo">20m</span>
                            </span>
                            <span class="clock-stat-inline">
                                <i class="fas fa-walking"></i>
                                <span class="session-type standing-label">Standing</span>
                                <span class="stat-value" id="standingTimeInfo">10m</span>
                            </span>
                        </div>
                    </div>

                    <div class="clock-main-content">
                        <!-- Left Side Image -->
                        <img src="/images/sitting-down/sitting_1.png"
                             alt="Sitting Position Left"
                             class="timer-side-image left sitting-image visible"
                             id="leftImage">

                        <div class="clock-display">
                            <div class="timer-progress">
                                <div class="progress-ring">
                                    <svg width="360" height="360" viewBox="0 0 360 360">
                                        <circle cx="180" cy="180" r="170" class="progress-ring-background"/>
                                        <circle cx="180" cy="180" r="170" class="progress-ring-fill" id="progressRing"/>
                                    </svg>
                                </div>
                                <div class="timer-display">
                                    <span class="time-left" id="timeDisplay">20:00</span>
                                    <div class="session-indicator" id="sessionIndicator">
                                        <i class="fas fa-chair session-icon"></i>
                                        <span class="session-label">Sit Down</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Side Image -->
                        <img src="/images/sitting-down/sitting_2.png"
                             alt="Sitting Position Right"
                             class="timer-side-image right sitting-image visible"
                             id="rightImage">

                        <div class="clock-controls">
                            <button class="btn-clock btn-start" id="startBtn">
                                <i class="fas fa-play"></i>
                                <span>Start</span>
                            </button>
                            <button class="btn-clock btn-pause" id="pauseBtn" disabled>
                                <i class="fas fa-pause"></i>
                                <span>Pause</span>
                            </button>
                            <button class="btn-clock btn-stop" id="stopBtn" disabled>
                                <i class="fas fa-stop"></i>
                                <span>Reset</span>
                            </button>
                            <button class="btn-clock btn-settings" id="settingsBtn">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </button>
                            <!-- Single Stop Alarm Button - Hidden by default -->
                            <button class="btn-clock btn-stop-alarm" id="stopAlarmBtn" style="display: none;">
                                <i class="fas fa-bell-slash"></i>
                                <span>Stop Alarm</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Setup Modal -->
            <div class="clock-modal" id="setupModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>🪑 Desk Timer Setup</h3>
                        <p>Configure when to sit down and when to stand up for better health</p>
                    </div>

                    <div class="modal-body">
                        <div class="time-input-group">
                            <label for="sittingTimeInput">
                                <i class="fas fa-chair"></i>
                                Sitting Time (minutes)
                                <span class="recommended-info">💡 Recommended: 20 minutes (LINAK 20:10 pattern)</span>
                            </label>
                            <input type="number" id="sittingTimeInput" min="1" max="180" value="20" />
                        </div>

                        <div class="time-input-group">
                            <label for="standingTimeInput">
                                <i class="fas fa-walking"></i>
                                Standing Time (minutes)
                                <span class="recommended-info">💡 Recommended: 10 minutes (LINAK 20:10 pattern)</span>
                            </label>
                            <input type="number" id="standingTimeInput" min="1" max="60" value="10" />
                        </div>

                        <div class="health-info" id="healthInfo">
                            <div class="health-indicator good">
                                <i class="fas fa-check-circle"></i>
                                <span>Great balance for your health!</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn-modal btn-save" id="saveSettingsBtn">
                            <i class="fas fa-check"></i>
                            Start Timer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Settings Modal -->
            <div class="clock-modal" id="settingsModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>⚙️ Timer Settings</h3>
                        <button class="close-modal" id="closeSettingsBtn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="time-input-group">
                            <label for="editSittingTimeInput">
                                <i class="fas fa-chair"></i>
                                Sitting Work Time (minutes)
                                <span class="recommended-info">💡 Sweet spot: 20-30 minutes for sustained attention</span>
                            </label>
                            <input type="number" id="editSittingTimeInput" min="1" max="180" />
                        </div>

                        <div class="time-input-group">
                            <label for="editStandingTimeInput">
                                <i class="fas fa-walking"></i>
                                Standing Break Time (minutes)
                                <span class="recommended-info">💡 Even 10-15 minutes helps reset your posture and mind</span>
                            </label>
                            <input type="number" id="editStandingTimeInput" min="1" max="60" />
                        </div>

                        <div class="health-info" id="editHealthInfo">
                            <!-- Health info will be populated here -->
                        </div>

                        <div class="danger-zone">
                            <h4><i class="fas fa-exclamation-triangle"></i> Reset Zone</h4>
                            <button class="btn-danger" id="resetSettingsBtn">
                                <i class="fas fa-trash"></i>
                                Reset All Settings & History
                            </button>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn-modal btn-cancel" id="cancelSettingsBtn">Cancel</button>
                        <button class="btn-modal btn-save" id="updateSettingsBtn">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Insert the HTML between welcome container and footer
        const mainContent = document.querySelector('main.content');
        if (mainContent) {
            mainContent.insertAdjacentHTML('afterend', clockHTML);
        }
    }

    // Bind DOM elements
    bindElements() {
        this.elements = {
            // Main elements
            timeDisplay: document.getElementById('timeDisplay'),
            sessionIndicator: document.getElementById('sessionIndicator'),
            cycleNumber: document.getElementById('cycleNumber'),
            progressRing: document.getElementById('progressRing'),

            // Control buttons
            startBtn: document.getElementById('startBtn'),
            pauseBtn: document.getElementById('pauseBtn'),
            stopBtn: document.getElementById('stopBtn'),
            settingsBtn: document.getElementById('settingsBtn'),

            // Stats
            sittingTimeInfo: document.getElementById('sittingTimeInfo'),
            standingTimeInfo: document.getElementById('standingTimeInfo'),

            // Setup modal
            setupModal: document.getElementById('setupModal'),
            sittingTimeInput: document.getElementById('sittingTimeInput'),
            standingTimeInput: document.getElementById('standingTimeInput'),
            healthInfo: document.getElementById('healthInfo'),
            saveSettingsBtn: document.getElementById('saveSettingsBtn'),

            // Settings modal
            settingsModal: document.getElementById('settingsModal'),
            editSittingTimeInput: document.getElementById('editSittingTimeInput'),
            editStandingTimeInput: document.getElementById('editStandingTimeInput'),
            editHealthInfo: document.getElementById('editHealthInfo'),
            closeSettingsBtn: document.getElementById('closeSettingsBtn'),
            updateSettingsBtn: document.getElementById('updateSettingsBtn'),
            cancelSettingsBtn: document.getElementById('cancelSettingsBtn'),
            resetSettingsBtn: document.getElementById('resetSettingsBtn'),

            // Alarm controls
            stopAlarmBtn: document.getElementById('stopAlarmBtn')
        };
        
        // Debug: Check if alarm button was found
        console.log('🔍 Stop alarm button found:', this.elements.stopAlarmBtn);
        if (!this.elements.stopAlarmBtn) {
            console.error('❌ Stop alarm button not found in DOM!');
        }
    }

    // Setup event listeners
    setupEventListeners() {
        // Control buttons
        this.elements.startBtn.addEventListener('click', () => this.startTimer());
        this.elements.pauseBtn.addEventListener('click', () => this.pauseTimer());
        this.elements.stopBtn.addEventListener('click', () => this.stopTimer());
        this.elements.settingsBtn.addEventListener('click', () => this.showSettingsModal());

        // Setup modal
        this.elements.saveSettingsBtn.addEventListener('click', () => this.saveInitialSettings());
        this.elements.sittingTimeInput.addEventListener('input', () => this.validateSetupInputs());
        this.elements.standingTimeInput.addEventListener('input', () => this.validateSetupInputs());

        // Settings modal
        this.elements.closeSettingsBtn.addEventListener('click', () => this.hideSettingsModal());
        this.elements.cancelSettingsBtn.addEventListener('click', () => this.hideSettingsModal());
        this.elements.updateSettingsBtn.addEventListener('click', () => this.updateSettings());
        this.elements.resetSettingsBtn.addEventListener('click', () => this.resetSettings());
        this.elements.editSittingTimeInput.addEventListener('input', () => this.validateEditInputs());
        this.elements.editStandingTimeInput.addEventListener('input', () => this.validateEditInputs());

        // Alarm control events
        if (this.elements.stopAlarmBtn) {
            this.elements.stopAlarmBtn.addEventListener('click', () => this.stopAlarm());
            console.log('✅ Stop alarm button event listener attached');
        } else {
            console.error('❌ Cannot attach event listener - stop alarm button not found');
        }

        // Modal backdrop clicks
        this.elements.setupModal.addEventListener('click', (e) => {
            if (e.target === this.elements.setupModal) {
                // Don't allow closing setup modal by clicking backdrop on first time
            }
        });

        this.elements.settingsModal.addEventListener('click', (e) => {
            if (e.target === this.elements.settingsModal) {
                this.hideSettingsModal();
            }
        });
    }

    // Setup core timer callbacks
    setupCoreCallbacks() {
        this.core.setCallbacks({
            onTick: (timeLeft, isSitting) => this.updateDisplay(timeLeft, isSitting),
            onSessionChange: (isSitting) => this.handleSessionChange(isSitting),
            onCycleComplete: (cycleCount) => this.handleCycleComplete(cycleCount)
        });
    }

    // Load saved settings
    loadSavedSettings() {
        const settings = this.storage.getSettings();
        console.log('📋 Loaded settings:', settings);
        console.log(`⏰ Initializing timer: ${settings.sittingTime} min sitting, ${settings.standingTime} min standing`);
        
        // Ensure minimum values to prevent timer loops
        settings.sittingTime = Math.max(1, settings.sittingTime);
        settings.standingTime = Math.max(1, settings.standingTime);
        
        this.core.initialize(settings.sittingTime, settings.standingTime);
        this.updateStatsDisplay();
        this.updateDisplay(settings.sittingTime * 60, true);

        // Load user's points from backend
        this.loadPointsStatus();
    }

    // Show setup modal for first-time users
    showSetupModal() {
        this.elements.setupModal.style.display = 'flex';
        this.validateSetupInputs();
    }

    // Hide setup modal
    hideSetupModal() {
        this.elements.setupModal.style.display = 'none';
    }

    // Show settings modal
    showSettingsModal() {
        const settings = this.storage.getSettings();
        this.elements.editSittingTimeInput.value = settings.sittingTime;
        this.elements.editStandingTimeInput.value = settings.standingTime;
        this.elements.settingsModal.style.display = 'flex';
        this.validateEditInputs();
    }

    // Hide settings modal
    hideSettingsModal() {
        this.elements.settingsModal.style.display = 'none';
    }

    // Validate setup inputs (no strict minimums)
    validateSetupInputs() {
        const sittingTime = parseInt(this.elements.sittingTimeInput.value) || 20;
        const standingTime = parseInt(this.elements.standingTimeInput.value) || 10;

        // Ensure positive values
        if (sittingTime < 1) this.elements.sittingTimeInput.value = 1;
        if (standingTime < 1) this.elements.standingTimeInput.value = 1;

        this.updateHealthInfo(sittingTime, standingTime, this.elements.healthInfo);
    }

    // Validate edit inputs (no strict minimums)
    validateEditInputs() {
        const sittingTime = parseInt(this.elements.editSittingTimeInput.value) || 20;
        const standingTime = parseInt(this.elements.editStandingTimeInput.value) || 10;

        this.updateHealthInfo(sittingTime, standingTime, this.elements.editHealthInfo);
    }

    // Update health info display
    updateHealthInfo(sittingTime, standingTime, container) {
        const validation = FocusClockCore.validateHealthyRatio(sittingTime, standingTime);

        container.innerHTML = `
            <div class="health-indicator ${validation.level}">
                <i class="fas fa-${validation.isHealthy ? 'check-circle' : (validation.level === 'warning' ? 'exclamation-triangle' : 'info-circle')}"></i>
                <span>${validation.recommendation}</span>
            </div>
        `;
    }

    // Save initial settings from setup modal
    saveInitialSettings() {
        const sittingTime = Math.max(1, parseInt(this.elements.sittingTimeInput.value) || 20);
        const standingTime = Math.max(1, parseInt(this.elements.standingTimeInput.value) || 10);

        this.storage.updateTimes(sittingTime, standingTime);
        this.storage.markAsConfigured();
        this.core.initialize(sittingTime, standingTime);

        this.hideSetupModal();
        this.updateStatsDisplay();
        this.updateDisplay(sittingTime * 60, true);
    }

    // Update settings from settings modal
    updateSettings() {
        const sittingTime = Math.max(1, parseInt(this.elements.editSittingTimeInput.value) || 20);
        const standingTime = Math.max(1, parseInt(this.elements.editStandingTimeInput.value) || 10);

        this.storage.updateTimes(sittingTime, standingTime);
        this.core.updateTimes(sittingTime, standingTime);

        this.hideSettingsModal();
        this.updateStatsDisplay();
    }

    // Reset all settings
    resetSettings() {
        if (confirm('Are you sure you want to reset all settings? This will clear your cycle history.')) {
            this.storage.resetSettings();
            this.core.pause();
            // Use default 20:10 pattern (LINAK)
            this.core.initialize(20, 10);
            this.updateStatsDisplay();
            this.updateDisplay(20 * 60, true);
            this.hideSettingsModal();
            this.updateButtonStates(false);
        }
    }

    // Timer control methods
    startTimer() {
        console.log('▶️ Starting timer...');
        const state = this.core.getCurrentSession();
        console.log('Starting state:', state);
        this.core.start();
        
        // Unlock audio autoplay for future alarm plays
        if (this.alarmAudio) {
            this.alarmAudio.play().then(() => {
                this.alarmAudio.pause();
                this.alarmAudio.currentTime = 0;
            }).catch(() => {
                // Ignore if autoplay is blocked
            });
        }
        
        this.updateButtonStates(true);
    }

    pauseTimer() {
        this.core.pause();
        this.updateButtonStates(false);
    }

    stopTimer() {
        this.core.stop();
        this.updateButtonStates(false);
    }

    // Update button states
    updateButtonStates(isRunning) {
        this.elements.startBtn.disabled = isRunning;
        this.elements.pauseBtn.disabled = !isRunning;
        this.elements.stopBtn.disabled = !isRunning;
    }

    // Update main display
    updateDisplay(timeLeft, isSitting) {
        console.log(`Display update - Time: ${FocusClockCore.formatTime(timeLeft)}, Session: ${isSitting ? 'Sitting' : 'Standing'}`);
        
        // Update time display
        this.elements.timeDisplay.textContent = FocusClockCore.formatTime(timeLeft);

        // Update session indicator
        const icon = this.elements.sessionIndicator.querySelector('.session-icon');
        const label = this.elements.sessionIndicator.querySelector('.session-label');

        if (isSitting) {
            icon.className = 'fas fa-chair session-icon';
            label.textContent = 'Sit Down';
        } else {
            icon.className = 'fas fa-walking session-icon';
            label.textContent = 'Stand Up';
        }

        // Update side images based on session
        this.updateSideImages(isSitting);

        // Update progress ring
        this.updateProgressRing(timeLeft, isSitting);

        // Update container class for styling
        const container = document.querySelector('.clock-container');
        container.className = `clock-container ${isSitting ? 'sitting-session' : 'standing-session'}`;
    }

    // Update side images
    updateSideImages(isSitting) {
        const leftImage = document.getElementById('leftImage');
        const rightImage = document.getElementById('rightImage');

        if (leftImage && rightImage) {
            // Instantly switch images without fade animation
            if (isSitting) {
                leftImage.src = "/images/sitting-down/sitting_1.png";
                leftImage.alt = "Sitting Position Left";
                rightImage.src = "/images/sitting-down/sitting_2.png";
                rightImage.alt = "Sitting Position Right";
            } else {
                leftImage.src = "/images/standing-up/standing_1.png";
                leftImage.alt = "Standing Position Left";
                rightImage.src = "/images/standing-up/standing_2.png";
                rightImage.alt = "Standing Position Right";
            }
        }
    }

    // Update progress ring
    updateProgressRing(timeLeft, isSitting) {
        const totalTime = isSitting ? this.core.sittingTime * 60 : this.core.standingTime * 60;
        const progress = ((totalTime - timeLeft) / totalTime) * 100;
        const circumference = 2 * Math.PI * 170; // radius = 170
        const offset = circumference - (progress / 100) * circumference;

        this.elements.progressRing.style.strokeDasharray = circumference;
        this.elements.progressRing.style.strokeDashoffset = offset;
    }

    // Handle session change
    handleSessionChange(isSitting) {
        console.log(`🎯 handleSessionChange called with: ${isSitting ? 'Sitting' : 'Standing'}`);

        // Play alarm when switching to standing session
        if (!isSitting) {
            console.log('🔔 Time to stand up! Playing alarm and showing notification');
            this.showStandUpNotification();
            this.playAlarm();
        } else {
            console.log('💺 Back to sitting - stopping alarm');
            // Stop alarm when switching back to sitting (if still playing)
            this.stopAlarm();
        }
    }

    // Handle cycle completion
    async handleCycleComplete(cycleCount) {
        console.log('🔄 Cycle completed:', cycleCount);
        this.storage.incrementCycles();
        this.elements.cycleNumber.textContent = cycleCount;
        this.updateStatsDisplay();

        // Submit cycle to backend for scoring
        await this.submitHealthCycle(cycleCount);
    }

    // Submit completed health cycle to backend
    async submitHealthCycle(cycleNumber) {
        const settings = this.storage.getSettings();

        try {
            const response = await fetch('/api/health-cycle/complete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    sitting_minutes: settings.sittingTime,
                    standing_minutes: settings.standingTime,
                    cycle_number: cycleNumber
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update points display
                this.updatePointsDisplay(data.total_points, data.daily_points);

                // Show feedback notification
                this.showPointsFeedback(data);
            } else {
                // Daily limit reached
                this.showPointsFeedback(data);
            }
        } catch (error) {
            console.log('Points system unavailable (not logged in or network error)');
            // Silently fail if user is not logged in or backend is unavailable
        }
    }

    // Update points display in navbar
    updatePointsDisplay(totalPoints, dailyPoints) {
        const totalPointsEl = document.getElementById('totalPoints');
        const dailyPointsEl = document.getElementById('dailyPoints');

        if (totalPointsEl) {
            totalPointsEl.textContent = totalPoints.toLocaleString();
        }

        if (dailyPointsEl) {
            const color = dailyPoints >= 100 ? '#FFD700' : 'rgba(255,255,255,0.8)';
            dailyPointsEl.textContent = `${dailyPoints}/100 today`;
            dailyPointsEl.style.color = color;
        }
    }

    // Show points feedback notification
    showPointsFeedback(data) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'points-notification';
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 10000;
            min-width: 300px;
            animation: slideIn 0.3s ease;
        `;

        const colorMap = {
            'green': '#10B981',
            'yellow': '#F59E0B',
            'orange': '#F97316',
            'red': '#EF4444',
            'blue': '#4A90E2'
        };

        const bgColor = colorMap[data.color] || '#4A90E2';

        notification.innerHTML = `
            <div style="display: flex; align-items: start; gap: 1rem;">
                <div style="width: 4px; height: 100%; background: ${bgColor}; border-radius: 2px;"></div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;">
                        ${data.points_earned > 0 ? `+${data.points_earned} Points! 🎉` : 'Cycle Complete'}
                    </div>
                    <div style="color: #666; margin-bottom: 0.5rem;">
                        Health Score: ${data.health_score}/100
                    </div>
                    <div style="color: #333; font-size: 0.9rem;">
                        ${data.feedback}
                    </div>
                    ${data.daily_limit_reached ? `
                        <div style="margin-top: 0.5rem; padding: 0.5rem; background: #FEF3C7; border-radius: 6px; font-size: 0.85rem; color: #92400E;">
                            🏆 Daily limit reached! Come back tomorrow for more points.
                        </div>
                    ` : ''}
                </div>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: #999; cursor: pointer; font-size: 1.2rem; padding: 0;">×</button>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Load user's points on page load
    async loadPointsStatus() {
        try {
            const response = await fetch('/api/health-cycle/points-status');
            const data = await response.json();

            this.updatePointsDisplay(data.total_points, data.daily_points);
        } catch (error) {
            console.log('Points system unavailable (not logged in)');
            // User not logged in - show default values
        }
    }

    // Update stats display
    updateStatsDisplay() {
        const settings = this.storage.getSettings();
        const stats = this.storage.getUsageStats();

        this.elements.sittingTimeInfo.textContent = `${settings.sittingTime}m`;
        this.elements.standingTimeInfo.textContent = `${settings.standingTime}m`;
        this.elements.cycleNumber.textContent = this.core.cycleCount;
    }

    // Alarm control methods - REWRITTEN FOR RELIABILITY
    playAlarm() {
        console.log('🔊 playAlarm called');
        
        // Play audio alarm
        if (this.alarmAudio && !this.isAlarmPlaying) {
            console.log('🎵 Starting alarm playback');
            this.isAlarmPlaying = true;
            this.alarmAudio.currentTime = 0;
            this.alarmAudio.play().catch(error => {
                console.warn('Failed to play alarm:', error);
                this.isAlarmPlaying = false;
            });
        }
        
        // Show alarm controls using multiple methods for reliability
        this.showAlarmControls();
        
        // FALLBACK: Also create a modal popup as backup (like the old working version)
        this.createAlarmModal();
    }

    stopAlarm() {
        console.log('🔕 stopAlarm called');
        
        // Stop audio
        if (this.alarmAudio && this.isAlarmPlaying) {
            this.isAlarmPlaying = false;
            this.alarmAudio.pause();
            this.alarmAudio.currentTime = 0;
        }

        // Hide all alarm controls
        this.hideAlarmControls();
        this.removeAlarmModal();

        // Clear any timeout
        if (this.alarmTimeout) {
            clearTimeout(this.alarmTimeout);
            this.alarmTimeout = null;
        }
    }

    showAlarmControls() {
        console.log('🔔 Showing alarm controls');
        
        // Method 1: Try to show existing button
        const stopAlarmBtn = document.getElementById('stopAlarmBtn');
        if (stopAlarmBtn) {
            stopAlarmBtn.style.display = 'flex';
            stopAlarmBtn.style.visibility = 'visible';
            console.log('✅ Existing alarm button shown');
        } else {
            console.warn('⚠️ Static alarm button not found, will use modal fallback');
        }
    }

    hideAlarmControls() {
        console.log('🔕 Hiding alarm controls');
        
        // Hide static button
        const stopAlarmBtn = document.getElementById('stopAlarmBtn');
        if (stopAlarmBtn) {
            stopAlarmBtn.style.display = 'none';
        }
    }

    // FALLBACK: Create modal popup (like the old working version)
    createAlarmModal() {
        // Remove any existing modal first
        this.removeAlarmModal();
        
        const modal = document.createElement('div');
        modal.id = 'alarmModal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        `;
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = `
            background: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            max-width: 400px;
            animation: modalSlideIn 0.3s ease;
        `;
        
        modalContent.innerHTML = `
            <div style="font-size: 3rem; margin-bottom: 1rem;">🚶‍♂️</div>
            <h2 style="margin-bottom: 1rem; color: #333;">Time to Stand Up!</h2>
            <p style="margin-bottom: 2rem; color: #666;">Take a ${this.core.standingTime}-minute standing break for better health.</p>
            <button id="stopAlarmModalBtn" style="
                background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
                color: white;
                border: none;
                padding: 1rem 2rem;
                border-radius: 8px;
                font-size: 1.1rem;
                font-weight: 600;
                cursor: pointer;
                box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
            ">
                <i class="fas fa-bell-slash" style="margin-right: 0.5rem;"></i>
                Stop Alarm
            </button>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        // Add event listener to modal button
        const modalBtn = document.getElementById('stopAlarmModalBtn');
        if (modalBtn) {
            modalBtn.addEventListener('click', () => this.stopAlarm());
            modalBtn.addEventListener('mouseover', function() {
                this.style.background = 'linear-gradient(135deg, #DC2626 0%, #B91C1C 100%)';
            });
            modalBtn.addEventListener('mouseout', function() {
                this.style.background = 'linear-gradient(135deg, #EF4444 0%, #DC2626 100%)';
            });
        }
        
        console.log('✅ Alarm modal created as fallback');
    }

    removeAlarmModal() {
        const existingModal = document.getElementById('alarmModal');
        if (existingModal) {
            existingModal.remove();
            console.log('🗑️ Alarm modal removed');
        }
    }

    // Show visual notification when it's time to stand up
    showStandUpNotification() {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'stand-up-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 10000;
            min-width: 280px;
            animation: slideIn 0.3s ease;
        `;

        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="font-size: 2rem;">🚶‍♂️</div>
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.2rem;">
                        Time to Stand Up!
                    </div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">
                        Take a ${this.core.standingTime}-minute standing break
                    </div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="background: rgba(255,255,255,0.2); border: none; color: white; 
                               cursor: pointer; font-size: 1.2rem; padding: 0.3rem; 
                               border-radius: 50%; width: 30px; height: 30px;">×</button>
            </div>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 8 seconds (alarm continues until manually stopped)
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, 8000);
    }

    // Show error if alarm audio fails to load
    showAlarmError() {
        console.warn('Alarm system: Audio file not found or failed to load');
        
        // Still show the visual notification even if audio fails
        this.showStandUpNotification();
        
        // Show a subtle error message
        const errorNotification = document.createElement('div');
        errorNotification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #FEF3C7;
            color: #92400E;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #F59E0B;
            z-index: 10000;
            max-width: 300px;
            font-size: 0.9rem;
        `;

        errorNotification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-size: 1.2rem;">⚠️</span>
                <span>Alarm audio unavailable. Visual notification shown instead.</span>
            </div>
        `;

        document.body.appendChild(errorNotification);

        setTimeout(() => errorNotification.remove(), 5000);
    }


}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if we're on the home page and elements exist
    if (document.querySelector('main.content')) {
        // Initialize the Focus Clock UI
        window.focusClockUI = new FocusClockUI();

        // Handle page visibility changes - ensure timer accuracy when tab becomes active
        document.addEventListener('visibilitychange', function() {
            if (!window.focusClockUI || !window.focusClockUI.core) return;

            if (!document.hidden && window.focusClockUI.core.isRunning) {
                // Tab became visible and timer is running - force an immediate update
                // This ensures the display is accurate even if intervals were throttled
                window.focusClockUI.core.tick();
            }
        });
    }
});

// Export for use in other files
window.FocusClockUI = FocusClockUI;