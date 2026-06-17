/**
 * bale/umpak — Alpine JS Components
 *
 * Cara pakai di landing page:
 *
 *   1. Publish asset:
 *      php artisan vendor:publish --tag="umpak:assets"
 *
 *   2. Import di resources/js/app.js sebelum Alpine.start():
 *      import '/vendor/umpak/umpak.js'
 *
 *   3. Atau load langsung di layout:
 *      <script defer src="{{ asset('vendor/umpak/umpak.js') }}"></script>
 *      (pastikan dimuat sebelum Alpine)
 */

/**
 * umpakNav — state management untuk navbar.
 *
 * Menyediakan:
 * - Mobile nav toggle (mobileOpen)
 * - Dropdown open/close per item (via index)
 *
 * Penggunaan:
 *   <nav x-data="umpakNav()">
 *     <button @click="toggleMobile()">Menu</button>
 *     <div x-show="mobileOpen">...</div>
 *
 *     @foreach($navLinks as $i => $link)
 *       <div @mouseenter="openDropdown({{ $i }})"
 *            @mouseleave="closeDropdown()">
 *         <div x-show="isDropdownOpen({{ $i }})">
 *           ...dropdown items...
 *         </div>
 *       </div>
 *     @endforeach
 *   </nav>
 */
// Tailwind CSS 4 Dark Mode Logic
function applyDefaultTheme() {
    const theme = localStorage.getItem('theme');
    const isDark = theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', isDark);
}

// Immediate execution to avoid FOUC
applyDefaultTheme();

// Re-apply on Livewire navigation
document.addEventListener('livewire:navigated', applyDefaultTheme);


document.addEventListener('alpine:init', () => {

    Alpine.data('umpakNav', () => ({
        mobileOpen: false,
        activeDropdown: null,
        isDark: document.documentElement.classList.contains('dark'),

        init() {
            // Watch for OS preference changes
            window.matchMedia("(prefers-color-scheme: dark)").addEventListener('change', (e) => {
                if (!localStorage.getItem('theme')) {
                    this.applyTheme(e.matches);
                }
            });
        },

        toggleTheme() {
            this.setTheme(this.isDark ? 'light' : 'dark');
        },

        setTheme(mode) {
            if (mode === 'system') {
                localStorage.removeItem('theme');
                this.applyTheme(window.matchMedia("(prefers-color-scheme: dark)").matches);
            } else {
                localStorage.setItem('theme', mode);
                this.applyTheme(mode === 'dark');
            }
        },

        applyTheme(isDark) {
            this.isDark = isDark;
            document.documentElement.classList.toggle('dark', isDark);
        },

        toggleMobile() {
            this.mobileOpen = !this.mobileOpen
        },

        closeMobile() {
            this.mobileOpen = false
        },

        openDropdown(id) {
            this.activeDropdown = id
        },

        closeDropdown() {
            this.activeDropdown = null
        },

        isDropdownOpen(id) {
            return this.activeDropdown === id
        },

        /**
         * Tutup mobile nav dan dropdown saat link diklik.
         * Berguna untuk SPA atau Livewire navigation.
         */
        onLinkClick() {
            this.mobileOpen = false
            this.activeDropdown = null
        },
    }))

})

