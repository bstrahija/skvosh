<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ArrowRight, PlayCircle } from 'lucide-vue-next';

defineProps<{
    auth?: {
        user?: any;
    };
}>();
</script>

<template>
    <!-- Hero Content -->
    <div class="px-6 pt-16 pb-24 lg:px-8 lg:pt-24">
        <div class="mx-auto max-w-4xl text-center">
            <div class="animate-fade-in-up animation-delay-300 opacity-0">
                <h1 class="text-5xl font-bold tracking-tight text-foreground sm:text-7xl">
                    Track Rankings &
                    <span class="bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent"> Reserve Courts </span>
                </h1>
            </div>

            <div class="animate-fade-in-up animation-delay-500 opacity-0">
                <p class="mt-6 text-xl leading-8 text-muted-foreground">
                    The ultimate platform for tracking player rankings and reserving courts across squash, tennis, badminton, and more. Join thousands
                    of athletes competing, improving, and booking their next game.
                </p>
            </div>

            <div class="animate-fade-in-up animation-delay-700 mt-10 flex items-center justify-center gap-4 opacity-0">
                <Button size="lg" class="group" as-child>
                    <a :href="route('dashboard')">
                        Start Playing
                        <ArrowRight class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                    </a>
                </Button>
                <Button variant="outline" size="lg" class="group" as-child>
                    <a href="#link-to-video-page">
                        <PlayCircle class="mr-2 h-4 w-4" />
                        Watch Demo
                    </a>
                </Button>
            </div>

            <!-- Stats -->
            <div class="animate-fade-in-up animation-delay-900 mt-16 grid grid-cols-2 gap-8 opacity-0 sm:grid-cols-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-foreground">5K+</div>
                    <div class="text-sm text-muted-foreground">Active Players</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-foreground">200+</div>
                    <div class="text-sm text-muted-foreground">Partner Clubs</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-foreground">1.5K+</div>
                    <div class="text-sm text-muted-foreground">Available Courts</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-foreground">50K+</div>
                    <div class="text-sm text-muted-foreground">Bookings Made</div>
                </div>
            </div>
        </div>
    </div>
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

.animation-delay-300 {
    animation-delay: 0.3s;
}

.animation-delay-500 {
    animation-delay: 0.5s;
}

.animation-delay-700 {
    animation-delay: 0.7s;
}

.animation-delay-900 {
    animation-delay: 0.9s;
}
</style>
