<script>
	function toggleMobileMenu() {
		document.getElementById('mobile-menu').classList.toggle('hidden');
	}
</script>

<!-- Mobile menu button -->
<button
	on:click={toggleMobileMenu}
	type="button"
	class="relative inline-flex items-center justify-center rounded-md bg-black p-2 text-white focus:outline-none focus:ring-2 focus:ring-white"
	aria-controls="mobile-menu"
	aria-expanded="false"
>
	<span class="absolute -inset-0.5"></span>
	<span class="sr-only">Open main menu</span>
	<!-- Menu open: "hidden", Menu closed: "block" -->
	<svg
		class="block h-6 w-6"
		fill="none"
		viewBox="0 0 24 24"
		stroke-width="1.5"
		stroke="currentColor"
		aria-hidden="true"
	>
		<path
			stroke-linecap="round"
			stroke-linejoin="round"
			d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
		/>
	</svg>
	<!-- Menu open: "block", Menu closed: "hidden" -->
	<svg
		class="hidden h-6 w-6"
		fill="none"
		viewBox="0 0 24 24"
		stroke-width="1.5"
		stroke="currentColor"
		aria-hidden="true"
	>
		<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
	</svg>
</button>
