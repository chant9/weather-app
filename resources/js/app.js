document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('onboarding-overlay');
    const dismiss = document.getElementById('onboarding-dismiss');

    function hideOnboarding() {
        overlay?.classList.add('hidden');
        overlay?.classList.remove('flex');
        window.dispatchEvent(new CustomEvent('onboarding-dismissed'));
    }

    function showOnboarding() {
        overlay?.classList.remove('hidden');
        overlay?.classList.add('flex');
    }

    dismiss?.addEventListener('click', hideOnboarding);

    document.addEventListener('livewire:init', () => {
        Livewire.on('locationSaved', hideOnboarding);
        Livewire.on('locationDeleted', ({ remaining }) => {
            if (remaining === 0) {
                showOnboarding();
            }
        });
    });
});
