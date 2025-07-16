<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Zap } from 'lucide-vue-next';

defineProps<{
    auth?: {
        user?: any;
    };
    appName?: string;
}>();
</script>

<template>
    <!-- Navigation -->
    <nav class="px-6 py-4 lg:px-8">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <div class="flex items-center space-x-2">
                <Zap class="h-8 w-8 text-primary" />
                <span class="text-2xl font-bold text-foreground">{{ appName || 'Skvosh' }}</span>
            </div>
            <div class="flex items-center space-x-4">
                <Button as-child v-if="!auth?.user" variant="ghost" class="animate-fade-in-up animation-delay-100 opacity-0">
                    <a :href="route('login')">
                        <span class="hidden sm:inline">Log In</span>
                    </a>
                </Button>
                <Button as-child v-else variant="ghost" class="animate-fade-in-up animation-delay-100 opacity-0">
                    <a :href="route('dashboard')">Dashboard</a>
                </Button>

                <Button as-child class="animate-fade-in-up animation-delay-200 opacity-0">
                    <a :href="route('dashboard')">Get Started</a>
                </Button>
            </div>
        </div>
    </nav>
</template>

<style scoped>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(2rem);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animation-delay-100 {
    animation-delay: 0.1s;
}

.animation-delay-200 {
    animation-delay: 0.2s;
}
</style>
