// Demontek Steam Template - Admin JavaScript v1.0

document.addEventListener('DOMContentLoaded', function() {
    initializeDemontekSteamAdmin();
});

function initializeDemontekSteamAdmin() {
    console.log('Demontek Steam Admin v1.0 initialized');
}

function previewSteamPost() {
    const postId = document.querySelector('input[name="post_ID"]').value;
    if (postId) {
        window.open(window.location.origin + '/?p=' + postId + '&demontek_preview=1', '_blank');
    }
}

function refreshFieldStatus() {
    const statusArea = document.querySelector('#demontek_steam_fields .inside');
    if (statusArea) {
        statusArea.innerHTML = '<p>?? Refreshing field status...</p>';
        // Implementation for AJAX refresh would go here
    }
}

// Global functions
window.previewSteamPost = previewSteamPost;
window.refreshFieldStatus = refreshFieldStatus;