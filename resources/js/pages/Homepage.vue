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
    <div class="bg-gradient-to-br from-background via-background to-muted">
        <!-- Navigation -->
        <nav class="px-6 py-4 lg:px-8">
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <div class="flex items-center space-x-2">
                    <Zap class="h-8 w-8 text-primary" />
                    <span class="text-2xl font-bold text-foreground">{{ $page.props?.name || 'Skvosh' }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <Button as-child v-if="!$page.props.auth?.user" variant="ghost" class="animate-fade-in-up animation-delay-100 opacity-0">
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

        <!-- Hero Content -->
        <div class="px-6 pt-16 pb-24 lg:px-8 lg:pt-24">
            <div class="mx-auto max-w-4xl text-center">
                <div class="animate-fade-in-up animation-delay-300 opacity-0">
                    <h1 class="text-5xl font-bold tracking-tight text-foreground sm:text-7xl">
                        Track Your
                        <span class="bg-gradient-to-r from-primary to-primary/80 bg-clip-text text-transparent"> Sports Rankings </span>
                    </h1>
                </div>

                <div class="animate-fade-in-up animation-delay-500 opacity-0">
                    <p class="mt-6 text-xl leading-8 text-muted-foreground">
                        The ultimate platform for tracking player rankings across squash, tennis, badminton, and more. Join thousands of athletes
                        competing and improving.
                    </p>
                </div>

                <div class="animate-fade-in-up animation-delay-700 mt-10 flex items-center justify-center gap-4 opacity-0">
                    <Button size="lg" class="group" as-child>
                        <a :href="route('dashboard')">
                            Start Ranking
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
                        <div class="text-3xl font-bold text-foreground">50K+</div>
                        <div class="text-sm text-muted-foreground">Matches Tracked</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-foreground">6</div>
                        <div class="text-sm text-muted-foreground">Sports Supported</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-foreground">99%</div>
                        <div class="text-sm text-muted-foreground">Uptime</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section class="bg-card py-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-4xl font-bold tracking-tight text-foreground">Everything you need to rank up</h2>
                <p class="mt-4 text-lg text-muted-foreground">
                    Professional-grade ranking system with real-time updates and comprehensive analytics.
                </p>
            </div>

            <div class="mx-auto mt-16 grid max-w-5xl grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10">
                            <Trophy class="h-6 w-6 text-primary" />
                        </div>
                        <CardTitle>Live Rankings</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">
                            Real-time ranking updates with ELO-based system for accurate skill assessment across all supported sports.
                        </p>
                    </CardContent>
                </Card>

                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-chart-2/10">
                            <Users class="h-6 w-6 text-chart-2" />
                        </div>
                        <CardTitle>Tournament Management</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">Create and manage tournaments with automated bracket generation and result tracking.</p>
                    </CardContent>
                </Card>

                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-chart-4/10">
                            <Activity class="h-6 w-6 text-chart-4" />
                        </div>
                        <CardTitle>Performance Analytics</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">
                            Detailed statistics and performance trends to help players analyze and improve their game.
                        </p>
                    </CardContent>
                </Card>

                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-chart-3/10">
                            <Target class="h-6 w-6 text-chart-3" />
                        </div>
                        <CardTitle>Multi-Sport Support</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">Support for squash, tennis, badminton, table tennis, pickleball, and paddle tennis.</p>
                    </CardContent>
                </Card>

                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-chart-1/10">
                            <TrendingUp class="h-6 w-6 text-chart-1" />
                        </div>
                        <CardTitle>Progress Tracking</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">Track your improvement over time with detailed match history and ranking progression.</p>
                    </CardContent>
                </Card>

                <Card class="group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <CardHeader>
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-chart-5/10">
                            <Star class="h-6 w-6 text-chart-5" />
                        </div>
                        <CardTitle>Player Profiles</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">Comprehensive player profiles with achievements, statistics, and match history.</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-muted py-24">
        <div class="mx-auto max-w-4xl px-6 text-center lg:px-8">
            <h2 class="text-4xl font-bold tracking-tight text-foreground">Ready to climb the rankings?</h2>
            <p class="mt-4 text-lg text-muted-foreground">Join thousands of players already tracking their progress with Skvosh.</p>
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
    <footer class="bg-background py-12">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex items-center justify-center space-x-2">
                <Zap class="h-6 w-6 text-primary" />
                <span class="text-xl font-bold text-foreground">Skvosh</span>
            </div>
            <p class="mt-4 text-center text-sm text-muted-foreground">© 2025 Skvosh. All rights reserved.</p>
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
