// Show the profile modal when the profile circle is clicked
function showProfileModal() {
    document.getElementById('profileModal').style.display = 'block';
}

// Close the profile modal when the close button is clicked
function closeProfileModal() {
    document.getElementById('profileModal').style.display = 'none';
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    if (event.target == document.getElementById('profileModal')) {
        closeProfileModal();
    }
}
