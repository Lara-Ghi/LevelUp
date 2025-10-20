// Rewards Page JavaScript
// Handles save/unsave toggle and user points checking

class RewardsManager {
    constructor() {
        this.savedRewards = this.loadSavedRewards();
        this.userPoints = 0;
        this.init();
    }

    init() {
        // Load user's total points
        this.loadUserPoints();

        // Initialize save buttons
        this.initializeSaveButtons();

        // Initialize redeem buttons
        this.updateRedeemButtons();

        // Handle tab switching to show saved rewards
        this.handleTabSwitching();
    }

    loadSavedRewards() {
        const saved = localStorage.getItem('savedRewards');
        return saved ? JSON.parse(saved) : [];
    }

    saveSavedRewards() {
        localStorage.setItem('savedRewards', JSON.stringify(this.savedRewards));
    }

    async loadUserPoints() {
        try {
            // Get user's total points from the navigation display
            const pointsElement = document.getElementById('totalPoints');
            if (pointsElement) {
                this.userPoints = parseInt(pointsElement.textContent) || 0;
            }

            // Alternatively, fetch from API if needed
            // const response = await fetch('/api/health-cycle/points-status');
            // const data = await response.json();
            // this.userPoints = data.total_points || 0;

            this.updateRedeemButtons();
        } catch (error) {
            console.error('Error loading user points:', error);
        }
    }

    initializeSaveButtons() {
        const saveButtons = document.querySelectorAll('.save-btn');

        saveButtons.forEach(button => {
            const rewardId = button.dataset.rewardId;

            // Set initial state based on saved rewards
            if (this.savedRewards.includes(rewardId)) {
                this.setSavedState(button, true);
            }

            // Add click event listener
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleSave(rewardId, button);
            });
        });
    }

    toggleSave(rewardId, button) {
        const isSaved = this.savedRewards.includes(rewardId);

        if (isSaved) {
            // Remove from saved
            this.savedRewards = this.savedRewards.filter(id => id !== rewardId);
            this.setSavedState(button, false);
        } else {
            // Add to saved
            this.savedRewards.push(rewardId);
            this.setSavedState(button, true);
        }

        this.saveSavedRewards();
        this.updateSavedTab();
    }

    setSavedState(button, isSaved) {
        const heartIcon = button.querySelector('.heart-icon');
        if (heartIcon) {
            heartIcon.src = isSaved
                ? '/images/giftcards/heart_checked.png'
                : '/images/giftcards/heart_unchecked.png';
        }
    }

    updateRedeemButtons() {
        const redeemButtons = document.querySelectorAll('.redeem-btn');

        redeemButtons.forEach(button => {
            const requiredPoints = parseInt(button.dataset.points);
            const btnText = button.querySelector('.btn-text');

            if (this.userPoints >= requiredPoints) {
                button.classList.add('can-redeem');
                if (btnText) btnText.textContent = 'Redeem';
            } else {
                button.classList.remove('can-redeem');
                if (btnText) btnText.textContent = 'Not Yet';
            }

            // Add click handler for redemption
            button.addEventListener('click', () => {
                if (this.userPoints >= requiredPoints) {
                    this.redeemReward(button, requiredPoints);
                } else {
                    this.showInsufficientPointsMessage(requiredPoints);
                }
            });
        });
    }

    redeemReward(button, requiredPoints) {
        const card = button.closest('.reward-card');
        const rewardName = card.querySelector('h3').textContent;

        // TODO: Implement actual redemption logic with backend
        const confirmed = confirm(`Redeem "${rewardName}" for ${requiredPoints} points?`);

        if (confirmed) {
            // Here you would make an API call to redeem the reward
            console.log(`Redeeming ${rewardName} for ${requiredPoints} points`);
            alert('Redemption successful! Check your email for details.');

            // Update user points
            this.userPoints -= requiredPoints;
            const pointsElement = document.getElementById('totalPoints');
            if (pointsElement) {
                pointsElement.textContent = this.userPoints;
            }

            this.updateRedeemButtons();
        }
    }

    showInsufficientPointsMessage(requiredPoints) {
        const pointsNeeded = requiredPoints - this.userPoints;
        alert(`You need ${pointsNeeded} more points to redeem this reward. Keep earning points!`);
    }

    handleTabSwitching() {
        // Check if we're on the saved tab
        const urlParams = new URLSearchParams(window.location.search);
        const currentTab = urlParams.get('tab');

        if (currentTab === 'saved') {
            this.updateSavedTab();
        }
    }

    updateSavedTab() {
        const savedGrid = document.getElementById('savedRewardsGrid');
        if (!savedGrid) return;

        // Clear existing content
        savedGrid.innerHTML = '';

        if (this.savedRewards.length === 0) {
            savedGrid.innerHTML = '<p class="no-saved-message">You haven\'t saved any rewards yet. Browse the "All" tab and click the heart icon to save your favorites!</p>';
            return;
        }

        // Clone saved reward cards from the "All" tab
        this.savedRewards.forEach(rewardId => {
            const originalCard = document.querySelector(`.reward-card[data-reward-id="${rewardId}"]`);
            if (originalCard) {
                const clonedCard = originalCard.cloneNode(true);

                // Reinitialize the save button for the cloned card
                const saveBtn = clonedCard.querySelector('.save-btn');
                if (saveBtn) {
                    this.setSavedState(saveBtn, true);
                    saveBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.toggleSave(rewardId, saveBtn);
                    });
                }

                // Reinitialize the redeem button for the cloned card
                const redeemBtn = clonedCard.querySelector('.redeem-btn');
                if (redeemBtn) {
                    const requiredPoints = parseInt(redeemBtn.dataset.points);
                    redeemBtn.addEventListener('click', () => {
                        if (this.userPoints >= requiredPoints) {
                            this.redeemReward(redeemBtn, requiredPoints);
                        } else {
                            this.showInsufficientPointsMessage(requiredPoints);
                        }
                    });
                }

                savedGrid.appendChild(clonedCard);
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const rewardsManager = new RewardsManager();

    // Update points when navigation points update (if using live updates)
    window.addEventListener('pointsUpdated', () => {
        rewardsManager.loadUserPoints();
    });
});
