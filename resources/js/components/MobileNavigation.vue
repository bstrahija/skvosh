<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Calendar, Home, Plus, Settings, Trophy } from 'lucide-vue-next';
import { computed } from 'vue';

const page = usePage();

// Get current route to highlight active nav item
const currentRoute = computed(() => page.url);

const navItems = [
    {
        name: 'Home',
        icon: Home,
        href: '/dashboard',
        active: currentRoute.value === '/dashboard',
    },
    {
        name: 'Matches',
        icon: Trophy,
        href: '/your-match',
        active: currentRoute.value === '/your-match',
    },
    {
        name: 'Create',
        icon: Plus,
        href: '/create',
        active: currentRoute.value === '/create',
    },
    {
        name: 'Reservations',
        icon: Calendar,
        href: '/reservations',
        active: currentRoute.value === '/reservations',
    },
    {
        name: 'Settings',
        icon: Settings,
        href: '/settings/profile',
        active: currentRoute.value.startsWith('/settings'),
    },
];
</script>

<template>
    <!-- Mobile Navigation Bar - Only visible on mobile devices -->
    <div class="fixed right-0 bottom-0 left-0 z-50 md:hidden">
        <div class="border-t bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="flex items-center justify-around px-2 py-2">
                <Link
                    v-for="item in navItems"
                    :key="item.name"
                    :href="item.href"
                    class="flex h-12 w-full flex-col items-center justify-center gap-1 rounded-lg px-1 py-1 text-xs transition-colors hover:bg-accent hover:text-accent-foreground"
                    :class="{
                        'bg-primary/10 text-primary': item.active,
                        'text-muted-foreground': !item.active,
                    }"
                >
                    <component :is="item.icon" class="h-5 w-5" />
                    <span class="text-xs font-medium">{{ item.name }}</span>
                </Link>
            </div>
        </div>
    </div>
</template>
