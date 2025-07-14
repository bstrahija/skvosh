<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head } from '@inertiajs/vue3';
import { Activity, ArrowRight, PlayCircle, Star, Target, TrendingUp, Trophy, Users, Zap } from 'lucide-vue-next';
</script>

<template>
    <Head title="Skvosh - Sports Ranking Platform">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-slate-50 via-white to-slate-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
        <!-- Navigation -->
        <nav class="px-6 py-4 lg:px-8">
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <div class="flex items-center space-x-2">
                    <Zap class="h-8 w-8 text-emerald-600" />
                    <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ $page.props?.name || 'Skvosh' }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <Button variant="ghost" class="animate-fade-in-up animation-delay-100 opacity-0"> Sign In </Button>
                    <Button class="animate-fade-in-up animation-delay-200 opacity-0"> Get Started </Button>
                </div>
            </div>
        </nav>

        <!-- Hero Content -->
        <div class="px-6 pt-16 pb-24 lg:px-8 lg:pt-24">
            <div class="mx-auto max-w-4xl text-center">
                <div class="animate-fade-in-up animation-delay-300 opacity-0">
                    <h1 class="text-5xl font-bold tracking-tight text-slate-900 sm:text-7xl dark:text-white">
                        Track Your
                        <span class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent"> Sports Rankings </span>
                    </h1>
                </div>

                <div class="animate-fade-in-up animation-delay-500 opacity-0">
                    <p class="mt-6 text-xl leading-8 text-slate-600 dark:text-slate-300">
                        The ultimate platform for tracking player rankings across squash, tennis, badminton, and more. Join thousands of athletes
                        competing and improving.
                    </p>
                </div>

                <div class="animate-fade-in-up animation-delay-700 mt-10 flex items-center justify-center gap-4 opacity-0">
                    <Button size="lg" class="group">
                        Start Ranking
                        <ArrowRight class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                    </Button>
                    <Button variant="outline" size="lg" class="group">
                        <PlayCircle class="mr-2 h-4 w-4" />
                        Watch Demo
                    </Button>
                </div>

                <!-- Stats -->
                <div class="animate-fade-in-up animation-delay-900 mt-16 grid grid-cols-2 gap-8 opacity-0 sm:grid-cols-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-slate-900 dark:text-white">5K+</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">Active Players</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-slate-900 dark:text-white">50K+</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">Matches Tracked</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-slate-900 dark:text-white">6</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">Sports Supported</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-slate-900 dark:text-white">99%</div>
                        <div class="text-sm text-slate-600 dark:text-slate-400">Uptime</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section class="bg-white py-24 dark:bg-slate-900">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white">Everything you need to rank up</h2>
                <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">
                    Professional-grade ranking system with real-time updates and comprehensive analytics.
                </p>
            </div>

            <div class="mx-auto mt-16 grid max-w-5xl grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/20">
                            <Trophy class="h-6 w-6 text-emerald-600" />
                        </div>
                        <CardTitle>Live Rankings</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-slate-600 dark:text-slate-300">
                            Real-time ranking updates with ELO-based system for accurate skill assessment across all supported sports.
                        </p>
                    </CardContent>
                </Card>

                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/20">
                            <Users class="h-6 w-6 text-blue-600" />
                        </div>
                        <CardTitle>Tournament Management</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-slate-600 dark:text-slate-300">
                            Create and manage tournaments with automated bracket generation and result tracking.
                        </p>
                    </CardContent>
                </Card>

                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-100 dark:bg-purple-900/20">
                            <Activity class="h-6 w-6 text-purple-600" />
                        </div>
                        <CardTitle>Performance Analytics</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-slate-600 dark:text-slate-300">
                            Detailed statistics and performance trends to help players analyze and improve their game.
                        </p>
                    </CardContent>
                </Card>

                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 dark:bg-orange-900/20">
                            <Target class="h-6 w-6 text-orange-600" />
                        </div>
                        <CardTitle>Multi-Sport Support</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-slate-600 dark:text-slate-300">
                            Support for squash, tennis, badminton, table tennis, pickleball, and paddle tennis.
                        </p>
                    </CardContent>
                </Card>

                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-teal-100 dark:bg-teal-900/20">
                            <TrendingUp class="h-6 w-6 text-teal-600" />
                        </div>
                        <CardTitle>Progress Tracking</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-slate-600 dark:text-slate-300">
                            Track your improvement over time with detailed match history and ranking progression.
                        </p>
                    </CardContent>
                </Card>

                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pink-100 dark:bg-pink-900/20">
                            <Star class="h-6 w-6 text-pink-600" />
                        </div>
                        <CardTitle>Player Profiles</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-slate-600 dark:text-slate-300">
                            Comprehensive player profiles with achievements, statistics, and match history.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-slate-50 py-24 dark:bg-slate-800">
        <div class="mx-auto max-w-4xl px-6 text-center lg:px-8">
            <h2 class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white">Ready to climb the rankings?</h2>
            <p class="mt-4 text-lg text-slate-600 dark:text-slate-300">Join thousands of players already tracking their progress with Skvosh.</p>
            <div class="mt-8 flex items-center justify-center gap-4">
                <Button size="lg" class="group">
                    Get Started Today
                    <ArrowRight class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                </Button>
                <Button variant="outline" size="lg"> View Pricing </Button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-12 dark:bg-slate-900">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex items-center justify-center space-x-2">
                <Zap class="h-6 w-6 text-emerald-600" />
                <span class="text-xl font-bold text-slate-900 dark:text-white">Skvosh</span>
            </div>
            <p class="mt-4 text-center text-sm text-slate-600 dark:text-slate-400">© 2025 Skvosh. All rights reserved.</p>
        </div>
    </footer>
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
