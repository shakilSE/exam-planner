</div><!-- end page-content -->
</div><!-- end main-content -->
</div><!-- end app-wrapper -->

<script>
// Mobile sidebar toggle
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
}

// Auto close alerts
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => {
        a.style.transition = 'opacity 0.5s';
        a.style.opacity = '0';
        setTimeout(() => a.remove(), 500);
    });
}, 4000);


// Confirm delete
function confirmDelete(url, name) {
    if (confirm('Are you sure you want to delete "' + name + '"?')) {
        window.location.href = url;
    }
}

// Modal helpers
function openModal(id) {
    document.getElementById(id).classList.add('show');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('show');
}
</script>
</body>
</html>
