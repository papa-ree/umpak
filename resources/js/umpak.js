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
document.addEventListener('alpine:init', () => {

    Alpine.data('umpakNav', () => ({
        mobileOpen: false,
        activeDropdown: null,

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
