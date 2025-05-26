import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist';

// Register Alpine plugins
Alpine.plugin(focus);
Alpine.plugin(persist);

// Initialize Alpine
window.Alpine = Alpine;

// Add Livewire hooks before starting Alpine
document.addEventListener('livewire:init', () => {
    Livewire.hook('message.processed', (message, component) => {
        // Re-initialize Alpine components after Livewire updates
        if (window.Alpine) {
            window.Alpine.initTree(document.body);
        }
    });
});

// Start Alpine after Livewire hooks are set up
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Alpine.isStarted) {
        window.Alpine.start();
    }
});
