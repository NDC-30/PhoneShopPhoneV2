
document.getElementById('toggleSidebar')?.addEventListener('click', function() {
    document.querySelector('.sidebar').classList.remove('show');
});

document.getElementById('toggleSidebarLarge')?.addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('show');
});

const handleLogout = function(e) {
    e.preventDefault();
    if (confirm('Bạn có chắc chắn muốn đăng xuất khỏi hệ thống?')) {
        alert('Đã đăng xuất thành công!'); 
    }
};

document.getElementById('logoutBtn')?.addEventListener('click', handleLogout);
document.getElementById('logoutTopbar')?.addEventListener('click', handleLogout);

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}
function formatDate(date) {
    return new Date(date).toLocaleDateString('vi-VN');
}

function getStatusBadgeClass(status) {
    const statusMap = {
        'Active': 'bg-success',
        'Inactive': 'bg-secondary',
        'Delivered': 'bg-success',
        'Processing': 'bg-warning',
        'Shipped': 'bg-info',
        'Cancelled': 'bg-danger',
        'Paid': 'bg-success',
        'Pending': 'bg-warning',
    };
    return statusMap[status] || 'bg-secondary';
}