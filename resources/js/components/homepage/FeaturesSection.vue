<script setup lang="ts">
import { Activity, Calendar, Clock, MapPin, Trophy, Users } from 'lucide-vue-next';
</script>

<template>
    <!-- Features Section -->
    <section class="relative overflow-hidden bg-muted/30 py-24">
        <!-- Noise Background -->
        <div class="bg-noise absolute inset-0 opacity-10"></div>

        <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-4xl font-bold tracking-tight text-foreground sm:text-5xl">Everything you need to play and compete</h2>
                <p class="mt-6 text-lg leading-8 text-muted-foreground">
                    Complete platform combining court reservations, player rankings, and tournament management in one place.
                </p>
            </div>

            <div class="mx-auto mt-20 grid max-w-6xl grid-cols-1 gap-x-8 gap-y-16 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Court Reservations -->
                <div
                    class="flex flex-col items-start rounded-lg border border-border/50 bg-card/60 p-6 backdrop-blur-sm transition-all duration-300 hover:border-border hover:bg-card/80 hover:shadow-sm"
                >
                    <div class="flex items-center space-x-3">
                        <div class="rounded-md bg-primary/10 p-2 ring-1 ring-primary/20">
                            <Calendar class="h-4 w-4 text-primary" />
                        </div>
                        <h3 class="text-xl leading-7 font-semibold text-foreground">Court Reservations</h3>
                    </div>
                    <p class="mt-4 text-base leading-7 text-muted-foreground">
                        Book courts instantly at partner clubs. Real-time availability, flexible scheduling, and seamless payment processing.
                    </p>
                </div>

                <!-- Live Rankings -->
                <div
                    class="flex flex-col items-start rounded-lg border border-border/50 bg-card/60 p-6 backdrop-blur-sm transition-all duration-300 hover:border-border hover:bg-card/80 hover:shadow-sm"
                >
                    <div class="flex items-center space-x-3">
                        <div class="rounded-md bg-chart-1/10 p-2 ring-1 ring-chart-1/20">
                            <Trophy class="h-4 w-4 text-chart-1" />
                        </div>
                        <h3 class="text-xl leading-7 font-semibold text-foreground">Live Rankings</h3>
                    </div>
                    <p class="mt-4 text-base leading-7 text-muted-foreground">
                        Real-time ranking updates with ELO-based system for accurate skill assessment across all supported sports.
                    </p>
                </div>

                <!-- Club Management -->
                <div
                    class="flex flex-col items-start rounded-lg border border-border/50 bg-card/60 p-6 backdrop-blur-sm transition-all duration-300 hover:border-border hover:bg-card/80 hover:shadow-sm"
                >
                    <div class="flex items-center space-x-3">
                        <div class="rounded-md bg-chart-2/10 p-2 ring-1 ring-chart-2/20">
                            <MapPin class="h-4 w-4 text-chart-2" />
                        </div>
                        <h3 class="text-xl leading-7 font-semibold text-foreground">Club Management</h3>
                    </div>
                    <p class="mt-4 text-base leading-7 text-muted-foreground">
                        Complete club administration tools. Manage multiple courts, set pricing, track bookings, and analyze usage patterns.
                    </p>
                </div>

                <!-- Tournament Management -->
                <div
                    class="flex flex-col items-start rounded-lg border border-border/50 bg-card/60 p-6 backdrop-blur-sm transition-all duration-300 hover:border-border hover:bg-card/80 hover:shadow-sm"
                >
                    <div class="flex items-center space-x-3">
                        <div class="rounded-md bg-chart-3/10 p-2 ring-1 ring-chart-3/20">
                            <Users class="h-4 w-4 text-chart-3" />
                        </div>
                        <h3 class="text-xl leading-7 font-semibold text-foreground">Tournament Management</h3>
                    </div>
                    <p class="mt-4 text-base leading-7 text-muted-foreground">
                        Create and manage tournaments with automated bracket generation and result tracking.
                    </p>
                </div>

                <!-- Performance Analytics -->
                <div
                    class="flex flex-col items-start rounded-lg border border-border/50 bg-card/60 p-6 backdrop-blur-sm transition-all duration-300 hover:border-border hover:bg-card/80 hover:shadow-sm"
                >
                    <div class="flex items-center space-x-3">
                        <div class="rounded-md bg-chart-4/10 p-2 ring-1 ring-chart-4/20">
                            <Activity class="h-4 w-4 text-chart-4" />
                        </div>
                        <h3 class="text-xl leading-7 font-semibold text-foreground">Performance Analytics</h3>
                    </div>
                    <p class="mt-4 text-base leading-7 text-muted-foreground">
                        Detailed statistics and performance trends to help players analyze and improve their game.
                    </p>
                </div>

                <!-- Smart Scheduling -->
                <div
                    class="flex flex-col items-start rounded-lg border border-border/50 bg-card/60 p-6 backdrop-blur-sm transition-all duration-300 hover:border-border hover:bg-card/80 hover:shadow-sm"
                >
                    <div class="flex items-center space-x-3">
                        <div class="rounded-md bg-chart-5/10 p-2 ring-1 ring-chart-5/20">
                            <Clock class="h-4 w-4 text-chart-5" />
                        </div>
                        <h3 class="text-xl leading-7 font-semibold text-foreground">Smart Scheduling</h3>
                    </div>
                    <p class="mt-4 text-base leading-7 text-muted-foreground">
                        AI-powered court recommendations based on your preferences, skill level, and playing history.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
