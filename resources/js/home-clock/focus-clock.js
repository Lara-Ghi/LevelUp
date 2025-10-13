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
        this.currentAlarm = null; // Track current alarm audio
        this.currentPopup = null; // Track current popup
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

    // Start or resume the timer
    start() {
        if (this.isRunning) return;

        // Clear any existing interval first
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }

        // If resuming from pause, use existing currentTime
        // If starting fresh, set up new session duration
        if (!this.sessionStartTime || this.currentTime >= this.sessionDuration) {
            this.sessionDuration = this.isSittingSession ? this.sittingTime * 60 : this.standingTime * 60;
            this.currentTime = this.sessionDuration;
        }
        
        // Update the start time and status immediately
        this.sessionStartTime = Date.now() - ((this.sessionDuration - this.currentTime) * 1000);
        this.isRunning = true;
        
        // Start the interval and update display immediately
        this.callbacks.onTick(this.currentTime, this.isSittingSession);
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
        this.sessionStartTime = null; // This is key for distinguishing between fresh start and resume
        this.callbacks.onTick(this.currentTime, this.isSittingSession);
    }

    // Timer tick function - always calculates based on session start time
    tick() {
        if (!this.isRunning || !this.sessionStartTime) return;

        // Calculate elapsed time since session started
        const now = Date.now();
        const elapsedSeconds = Math.floor((now - this.sessionStartTime) / 1000);
        const newTime = Math.max(0, this.sessionDuration - elapsedSeconds);
        
        // Only update if time has actually changed
        if (newTime !== this.currentTime) {
            this.currentTime = newTime;
            // Update display
            this.callbacks.onTick(this.currentTime, this.isSittingSession);

            // Check if we need to switch sessions
            if (this.currentTime <= 0) {
                // Prevent multiple switches
                if (this.intervalId) {
                    clearInterval(this.intervalId);
                    this.intervalId = null;
                }
                this.currentTime = 0;
                this.completeSession();
                return;
            }
        }
    }

    // Complete current session and switch
    completeSession() {
        console.log(`🔄 Completing session - Current state: ${this.isSittingSession ? 'Sitting' : 'Standing'}`);
        
        // Validate timer settings before proceeding
        if (this.sittingTime <= 0 || this.standingTime <= 0) {
            console.error('❌ Invalid timer settings detected!', {
                sitting: this.sittingTime,
                standing: this.standingTime
            });
            return;
        }

        // Stop the current timer
        clearInterval(this.intervalId);
        this.intervalId = null;
        this.isRunning = false;
        
        const wasSitting = this.isSittingSession;
        this.isSittingSession = !wasSitting;  // Switch session type

        // Clean up any existing alarm and popup before switching
        this.cleanupAlarmAndPopup();

        if (wasSitting) {
            // Sitting session completed, switch to standing
            console.log('✅ Switching from sitting to standing');
            
            // Play alarm and show popup for standing break (energetic notification)
            this.playAlarmAndShowPopup('standUp');
        } else {
            // Standing session completed, switch to sitting
            console.log('✅ Switching from standing to sitting');
            
            // Increment cycle count when FULL cycle completes (sitting + standing)
            this.cycleCount++;
            this.callbacks.onCycleComplete(this.cycleCount);
            
            // Play alarm and show popup for back to work (same as stand-up but different audio)
            this.playAlarmAndShowPopup('backToWork');
        }

        // Update display and trigger callbacks before starting new session
        this.callbacks.onSessionChange(this.isSittingSession);
        
        // Clear any existing interval and reset session state
        this.sessionDuration = this.isSittingSession ? this.sittingTime * 60 : this.standingTime * 60;
        this.currentTime = this.sessionDuration;
        this.sessionStartTime = Date.now();
        this.isRunning = true;
        
        // Start new interval
        this.intervalId = setInterval(() => this.tick(), 100);
        
        // Update display immediately with new session
        this.callbacks.onTick(this.currentTime, this.isSittingSession);
    }
    
    // Get alarm sound based on session type
    getAlarmSoundForSession(sessionType = 'standUp') {
        // Different sounds for different transitions
        const alarmSounds = {
            standUp: '/alarm_files/alarm_1.mp3',         // Energetic sound for standing break
            backToWork: '/alarm_files/alarm_2.mp3',     // Gentler sound for back to work
            focused: '/alarm_files/alarm_1.mp3',        // Use alarm_1 as fallback
            gentle: '/alarm_files/alarm_2.mp3'          // Use alarm_2 as fallback
        };

        return alarmSounds[sessionType] || alarmSounds.standUp;
    }

    // Get popup content based on session type
    getPopupContentForSession(sessionType) {
        const content = {
            standUp: {
                icon: '🚶‍♂️',
                title: 'Stand Up Break!',
                message: `${this.standingTime}-min break to stretch and move around`,
                buttonText: 'Stop Alarm',
                buttonColor: '#EF4444'
            },
            backToWork: {
                icon: '💺',
                title: 'Back to Work',
                message: `Time for ${this.sittingTime} minutes of focused work`,
                buttonText: 'Got It',
                buttonColor: '#3B82F6'
            },
            focused: {
                icon: '🎯',
                title: 'Focus Time',
                message: `Deep work session - ${this.sittingTime} minutes of concentration`,
                buttonText: 'Start Focusing',
                buttonColor: '#059669'
            }
        };

        return content[sessionType] || content.standUp;
    }



    // Clean up any existing alarm and popup
    cleanupAlarmAndPopup() {
        console.log('🧹 Cleaning up existing alarm and popup');
        
        // Stop and cleanup alarm
        if (this.currentAlarm) {
            this.currentAlarm.pause();
            this.currentAlarm.currentTime = 0;
            this.currentAlarm = null;
        }

        // Immediately remove all existing modals to prevent overlap
        const existingModals = document.querySelectorAll('.clock-modal');
        existingModals.forEach(modal => {
            if (modal && modal.parentNode) {
                modal.remove();
            }
        });

        // Clear the reference
        this.currentPopup = null;
        
        console.log('✅ Cleanup completed');
    }

    // Play alarm and show popup
    playAlarmAndShowPopup(sessionType = 'standUp') {
        // Clean up any existing alarm and popup first
        this.cleanupAlarmAndPopup();
        
        // Add a small delay to ensure cleanup is complete
        setTimeout(() => {
            this.createNewAlarmPopup(sessionType);
        }, 100);
    }

    // Create new alarm popup (separated for better control)
    createNewAlarmPopup(sessionType) {
        console.log(`🔔 Creating ${sessionType} popup`);
        
        // Choose alarm sound based on session type
        const alarmSound = this.getAlarmSoundForSession(sessionType);
        
        // Create and configure alarm with high priority
        this.currentAlarm = new Audio(alarmSound);
        this.currentAlarm.loop = sessionType === 'standUp'; // Only loop for stand-up alarms
        this.currentAlarm.volume = 1.0;
        this.currentAlarm.setAttribute('autoplay', 'true');
        this.currentAlarm.setAttribute('preload', 'auto');
        
        // Function to ensure alarm plays
        const ensureAlarmPlays = () => {
            const playPromise = this.currentAlarm.play();
            if (playPromise !== undefined) {
                playPromise.catch(error => {
                    console.warn('Playback failed, will retry:', error);
                    // Retry on next user interaction
                    const retryPlay = () => {
                        if (this.currentAlarm) {
                            this.currentAlarm.play().catch(console.warn);
                        }
                        document.removeEventListener('click', retryPlay);
                    };
                    document.addEventListener('click', retryPlay);
                });
            }
        };

        // Try to play immediately
        ensureAlarmPlays();

        // Auto-dismiss popup when non-looping audio ends
        if (sessionType !== 'standUp') {
            this.currentAlarm.addEventListener('ended', () => {
                console.log('🎵 Audio finished naturally, auto-dismissing popup');
                this.cleanupAlarmAndPopup();
            });
        }

        // Create a completely fresh popup element
        this.currentPopup = document.createElement('div');
        this.currentPopup.className = 'clock-modal';
        this.currentPopup.style.display = 'flex';
        
        // Get content based on session type
        const popupContent = this.getPopupContentForSession(sessionType);
        console.log(`📋 Popup content for ${sessionType}:`, popupContent);
        
        this.currentPopup.innerHTML = `
            <div class="modal-content" style="max-width: 300px; padding: 1rem;">
                <div class="modal-header" style="padding: 0 0 0.5rem 0; margin: 0;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span style="font-size: 1.5rem;">${popupContent.icon}</span>
                        <h3 style="margin: 0; font-size: 1.1rem;">${popupContent.title}</h3>
                    </div>
                </div>
                <div class="modal-body" style="padding: 0.5rem 0;">
                    <p style="margin: 0 0 1rem 0; font-size: 0.9rem; color: #666;">
                        ${popupContent.message}
                    </p>
                    
                    <!-- Volume Control in Popup -->
                    <div class="popup-volume-control" style="margin: 1rem 0; 
                                                             padding: 0.75rem; 
                                                             background: rgba(0,0,0,0.05); 
                                                             border-radius: 8px;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fas fa-volume-up popup-volume-icon" style="color: #6B7280; min-width: 16px;"></i>
                            <input type="range" 
                                   class="popup-volume-slider"
                                   min="0" 
                                   max="100" 
                                   value="100"
                                   step="1"
                                   style="flex: 1; 
                                          height: 4px; 
                                          border-radius: 2px; 
                                          background: #E5E7EB; 
                                          outline: none; 
                                          cursor: pointer; 
                                          accent-color: ${popupContent.buttonColor}; 
                                          -webkit-appearance: none;">
                            <span class="popup-volume-percentage" style="font-size: 0.8rem; 
                                                                          color: #6B7280; 
                                                                          min-width: 35px; 
                                                                          text-align: center;">100%</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 0.5rem 0 0 0; margin: 0;">
                    <button class="stop-alarm-btn" 
                            style="width: 100%; 
                                   padding: 0.5rem; 
                                   font-size: 0.9rem; 
                                   display: flex; 
                                   align-items: center; 
                                   justify-content: center; 
                                   gap: 0.5rem;
                                   background: ${popupContent.buttonColor};
                                   color: white;
                                   border: none;
                                   border-radius: 0.375rem;
                                   cursor: pointer;">
                        <i class="fas fa-bell-slash"></i>
                        ${popupContent.buttonText}
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(this.currentPopup);

        // Set up event listeners directly here
        const stopBtn = this.currentPopup.querySelector('.stop-alarm-btn');
        const volumeSlider = this.currentPopup.querySelector('.popup-volume-slider');
        const volumeIcon = this.currentPopup.querySelector('.popup-volume-icon');
        const volumePercentage = this.currentPopup.querySelector('.popup-volume-percentage');
        
        if (stopBtn) {
            console.log('✅ Stop button found, setting up event listener');
            stopBtn.addEventListener('click', (e) => {
                console.log('🔴 Stop button clicked!');
                e.preventDefault();
                e.stopPropagation();
                this.cleanupAlarmAndPopup();
            });
        } else {
            console.warn('Stop button not found in popup');
        }

        // Set up volume control
        if (volumeSlider && volumeIcon && volumePercentage && this.currentAlarm) {
            volumeSlider.addEventListener('input', (e) => {
                const volume = parseFloat(e.target.value) / 100;
                this.currentAlarm.volume = volume;
                
                // Update volume icon
                if (volume === 0) {
                    volumeIcon.className = 'fas fa-volume-mute popup-volume-icon';
                } else if (volume < 0.5) {
                    volumeIcon.className = 'fas fa-volume-down popup-volume-icon';
                } else {
                    volumeIcon.className = 'fas fa-volume-up popup-volume-icon';
                }
                
                // Update percentage display
                volumePercentage.textContent = Math.round(volume * 100) + '%';
                
                console.log('🔊 Popup volume set to:', Math.round(volume * 100) + '%');
            });
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


        };
    }

    // Setup event listeners
    setupEventListeners() {
        // Control buttons
        this.elements.startBtn.addEventListener('click', () => this.startTimer());
        this.elements.pauseBtn.addEventListener('click', () => this.pauseTimer());
        this.elements.stopBtn.addEventListener('click', () => this.stopTimer());
        this.elements.settingsBtn.addEventListener('click', () => {
            console.log('⚙️ Settings button clicked!');
            try {
                this.showSettingsModal();
            } catch (error) {
                console.error('Error opening settings modal:', error);
            }
        });

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
        console.log('📝 Opening settings modal...');
        try {
            const settings = this.storage.getSettings();
            console.log('Settings loaded:', settings);
            
            if (!this.elements.editSittingTimeInput || !this.elements.editStandingTimeInput || !this.elements.settingsModal) {
                console.error('Settings modal elements not found:', {
                    editSittingTimeInput: !!this.elements.editSittingTimeInput,
                    editStandingTimeInput: !!this.elements.editStandingTimeInput,
                    settingsModal: !!this.elements.settingsModal
                });
                return;
            }
            
            this.elements.editSittingTimeInput.value = settings.sittingTime;
            this.elements.editStandingTimeInput.value = settings.standingTime;
            this.elements.settingsModal.style.display = 'flex';
            console.log('✅ Settings modal should now be visible');
            this.validateEditInputs();
        } catch (error) {
            console.error('Error in showSettingsModal:', error);
        }
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
        const hasPausedTime = !isRunning && this.core.currentTime < this.core.sessionDuration;
        this.elements.startBtn.disabled = isRunning;
        this.elements.pauseBtn.disabled = !isRunning;
        this.elements.stopBtn.disabled = !isRunning && !hasPausedTime; // Enable reset when paused
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
        // Session change is now handled by the core timer
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

        // Setup popup event listeners
    setupPopupEventListeners(popup) {
        if (!popup) {
            console.warn('No popup provided to setupPopupEventListeners');
            return;
        }

        const stopBtn = popup.querySelector('.stop-alarm-btn');
        if (!stopBtn) {
            console.warn('Stop button not found in popup - available classes:', popup.innerHTML);
            return;
        }

        console.log('✅ Stop button found, setting up event listener');
        
        // Simple click handler to stop alarm
        stopBtn.addEventListener('click', (e) => {
            console.log('🔴 Stop button clicked!');
            e.preventDefault();
            e.stopPropagation();
            this.cleanupAlarmAndPopup();
        });
    }

    // Show points feedback notification - Removed
    showPointsFeedback(data) {
        // Update points display silently
        if (data.points_earned > 0) {
            this.updatePointsDisplay(data.total_points, data.daily_points);
        }
    }

    // Show visual notification when it's time to stand up - Removed
    showStandUpNotification() {
        // Removed notification
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











    // Show error if alarm audio fails to load

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