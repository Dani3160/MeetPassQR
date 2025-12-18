import Swal from 'sweetalert2';

// Helper function to get CSS variable value and convert to hex
const getPrimaryColorHex = () => {
    try {
        const root = document.documentElement;
        const primary = getComputedStyle(root).getPropertyValue('--primary').trim();

        // If it's already a hex color, use it
        if (primary && primary.startsWith('#')) {
            return primary;
        }

        // If it's oklch, we need to convert it
        // Create a temporary element to get computed color
        const tempEl = document.createElement('div');
        tempEl.style.position = 'absolute';
        tempEl.style.visibility = 'hidden';
        tempEl.style.color = primary || 'oklch(70.5% 0.213 47.604)';
        document.body.appendChild(tempEl);

        const computedColor = getComputedStyle(tempEl).color;
        document.body.removeChild(tempEl);

        // Convert rgb/rgba to hex
        if (computedColor && computedColor.startsWith('rgb')) {
            const rgb = computedColor.match(/\d+/g);
            if (rgb && rgb.length >= 3) {
                const hex = '#' + rgb.slice(0, 3).map(x => {
                    const h = parseInt(x).toString(16);
                    return h.length === 1 ? '0' + h : h;
                }).join('');
                return hex;
            }
        }
    } catch (e) {
        console.warn('Error getting primary color:', e);
    }

    // Fallback to default orange
    return '#F97316';
};

// Custom configuration untuk SweetAlert2 (will be merged with dynamic color)
const swalConfig = {
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya',
    cancelButtonText: 'Batal',
    buttonsStyling: true,
    customClass: {
        confirmButton: 'swal-confirm-btn',
        cancelButton: 'swal-cancel-btn',
        popup: 'swal-popup'
    },
    allowOutsideClick: false,
    allowEscapeKey: true
};

// Success Alert
export const showSuccess = (title, text = '', timer = 3000) => {
    return Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        timer: timer,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        confirmButtonColor: getPrimaryColorHex(), // Get current primary color
        customClass: {
            popup: 'swal-popup',
            confirmButton: 'swal-confirm-btn'
        }
    });
};

// Error Alert
export const showError = (title, text = '') => {
    return Swal.fire({
        icon: 'error',
        title: title,
        text: text,
        confirmButtonText: 'OK',
        confirmButtonColor: '#ef4444',
        customClass: {
            popup: 'swal-popup',
            confirmButton: 'swal-confirm-btn'
        }
    });
};

// Warning Alert
export const showWarning = (title, text = '') => {
    return Swal.fire({
        icon: 'warning',
        title: title,
        text: text,
        confirmButtonText: 'OK',
        confirmButtonColor: '#f59e0b',
        customClass: {
            popup: 'swal-popup',
            confirmButton: 'swal-confirm-btn'
        }
    });
};

// Info Alert
export const showInfo = (title, text = '') => {
    return Swal.fire({
        icon: 'info',
        title: title,
        text: text,
        confirmButtonText: 'OK',
        confirmButtonColor: '#3b82f6',
        customClass: {
            popup: 'swal-popup',
            confirmButton: 'swal-confirm-btn'
        }
    });
};

// Confirmation Dialog
export const showConfirm = (title, text = '', confirmText = 'Ya', cancelText = 'Batal') => {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        customClass: {
            popup: 'swal-popup',
            confirmButton: 'swal-confirm-btn',
            cancelButton: 'swal-cancel-btn'
        },
        ...swalConfig
    });
};

// Delete Confirmation (khusus untuk hapus)
export const showDeleteConfirm = (title = 'Hapus Data', text = 'Apakah Anda yakin ingin menghapus? Tindakan ini tidak dapat dibatalkan.') => {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        customClass: {
            popup: 'swal-popup',
            confirmButton: 'swal-confirm-btn swal-delete-btn',
            cancelButton: 'swal-cancel-btn'
        },
        ...swalConfig
    });
};

// Logout Confirmation
export const showLogoutConfirm = () => {
    return Swal.fire({
        title: 'Logout?',
        text: 'Apakah Anda yakin ingin logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        customClass: {
            popup: 'swal-popup',
            confirmButton: 'swal-confirm-btn',
            cancelButton: 'swal-cancel-btn'
        },
        ...swalConfig
    });
};

// Loading Alert
export const showLoading = (title = 'Memproses...') => {
    return Swal.fire({
        title: title,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        },
        customClass: {
            popup: 'swal-popup'
        }
    });
};

// Close Loading
export const closeLoading = () => {
    Swal.close();
};

export default {
    showSuccess,
    showError,
    showWarning,
    showInfo,
    showConfirm,
    showDeleteConfirm,
    showLogoutConfirm,
    showLoading,
    closeLoading
};
