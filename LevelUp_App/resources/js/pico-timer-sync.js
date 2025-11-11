/**
 * Pico W Timer Phase Synchronization
 * 
 * This script syncs the focus timer phase (sitting/standing) with the Pico W
 * so the RGB LED can show the correct color and displays warnings.
 */

// Send timer phase and time info to backend for Pico W
function updatePicoTimerPhase(phase, timeRemaining = null) {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    if (!csrfToken) {
        console.warn('⚠️ No CSRF token found, cannot update Pico timer phase');
        return;
    }

    const payload = { phase: phase };
    if (timeRemaining !== null) {
        payload.time_remaining = timeRemaining;
    }

    console.log(`📡 Sending timer phase to backend:`, payload);

    fetch('/api/pico/timer-phase', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log(`✅ Pico timer phase updated:`, data);
    })
    .catch(error => {
        console.error('❌ Failed to update Pico timer phase:', error);
    });
}

// Hook into the focus timer callbacks
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Pico timer sync script loaded');
    
    // Wait for focusClockUI to be initialized
    const checkFocusClock = setInterval(() => {
        if (window.focusClockUI && window.focusClockUI.core) {
            console.log('🔗 Hooking into focus timer for Pico sync');
            clearInterval(checkFocusClock);
            
            // Store original callback
            const originalOnTick = window.focusClockUI.core.callbacks.onTick;
            const originalOnSessionChange = window.focusClockUI.core.callbacks.onSessionChange;
            
            let lastPhase = null;
            let lastTimeUpdate = 0;
            
            // Wrap onTick to detect phase changes and send time updates
            window.focusClockUI.core.callbacks.onTick = function(timeLeft, isSitting) {
                // Call original callback
                if (originalOnTick) {
                    originalOnTick(timeLeft, isSitting);
                }
                
                // Send phase update if it changed
                const currentPhase = isSitting ? 'sitting' : 'standing';
                if (currentPhase !== lastPhase) {
                    updatePicoTimerPhase(currentPhase, timeLeft);
                    lastPhase = currentPhase;
                    lastTimeUpdate = Date.now();
                }
                
                // Send time updates every 5 seconds
                const now = Date.now();
                if (now - lastTimeUpdate >= 5000) {
                    updatePicoTimerPhase(currentPhase, timeLeft);
                    lastTimeUpdate = now;
                }
            };
            
            // Wrap onSessionChange
            window.focusClockUI.core.callbacks.onSessionChange = function(isSitting) {
                // Call original callback
                if (originalOnSessionChange) {
                    originalOnSessionChange(isSitting);
                }
                
                // Send phase update with initial time
                const phase = isSitting ? 'sitting' : 'standing';
                const timeLeft = window.focusClockUI.core.currentTime || 0;
                updatePicoTimerPhase(phase, timeLeft);
                lastPhase = phase;
                lastTimeUpdate = Date.now();
            };
            
            // Handle timer stop
            const originalStop = window.focusClockUI.core.stop.bind(window.focusClockUI.core);
            window.focusClockUI.core.stop = function() {
                originalStop();
                updatePicoTimerPhase(null, 0);
                lastPhase = null;
            };
        }
    }, 100);
    
    // Give up after 10 seconds
    setTimeout(() => clearInterval(checkFocusClock), 10000);
});
