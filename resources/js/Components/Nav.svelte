<script>
    import DarkModeToggle from "./DarkModeToggle.svelte";
    import NavGithubLink from "./NavGithubLink.svelte";
    import NavLink from "./NavLink.svelte";
    import NavMobileButton from "./NavMobileButton.svelte";
    import NavMobileProfile from "./NavMobileProfile.svelte";
    import NavProfileDropdown from "./NavProfileDropdown.svelte";
    import NavSiteLogo from "./NavSiteLogo.svelte";

    const navLinks = [
        { name: "By Game", href: "/browse/games" },
        { name: "Full List", href: "/browse/list" },
    ];
</script>

<nav class="bg-red-800">
    <div class="mx-3 xl:mx-auto max-w-7xl">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <NavSiteLogo />
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        {#each navLinks as link}
                            <NavLink href={link.href}>{link.name}</NavLink>
                        {/each}
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex space-x-4 items-center md:ml-6">
                    <DarkModeToggle />
                    <NavGithubLink />
                    <!-- <NavProfileDropdown /> -->
                </div>
            </div>
            <div class="-mr-2 flex space-x-5 items-center md:hidden">
                <DarkModeToggle />
                <NavGithubLink />
                <NavMobileButton />
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="md:hidden" id="mobile-menu">
        <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
            {#each navLinks as link}
                <NavLink href={link.href}>{link.name}</NavLink>
            {/each}
        </div>
        <!-- <NavMobileProfile /> -->
    </div>
</nav>
