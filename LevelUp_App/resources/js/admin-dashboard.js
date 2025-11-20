//Admin Dashboard Controller

class AdminDashboard {
    constructor() {
        this.init();
    }

    init() {
        // Initialize archived rewards toggle
        this.initializeArchivedToggle();
    }

    initializeArchivedToggle() {
        const toggleArchivedBtn = document.getElementById('toggleArchivedBtn');
        if (toggleArchivedBtn) {
            toggleArchivedBtn.addEventListener('click', function() {
                const section = document.getElementById('archivedRewardsSection');
                if (section.style.display === 'none' || section.style.display === '') {
                    section.style.display = 'block';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Archived';
                } else {
                    section.style.display = 'none';
                    this.innerHTML = '<i class="fas fa-archive"></i> Show Archived';
                }
            });
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AdminDashboard();
});