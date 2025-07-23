<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { CalendarDays, Clock, MapPin } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Your Match',
        href: '/your-match',
    },
];
</script>

<template>
    <Head title="Your Match" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold">Your Match</h1>
                <p class="text-muted-foreground">Manage your upcoming and recent matches</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Upcoming Match -->
                <Card class="relative overflow-hidden">
                    <div class="absolute inset-0 z-0">
                        <img
                            src="https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=600&h=400&fit=crop"
                            alt="Tennis court"
                            class="h-full w-full object-cover opacity-20"
                        />
                        <div class="absolute inset-0 bg-gradient-to-r from-background/95 to-background/90"></div>
                    </div>
                    <CardHeader class="relative z-10">
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-xl">Next Match</CardTitle>
                            <span class="rounded-full bg-primary px-3 py-1 text-xs font-medium text-primary-foreground">UPCOMING</span>
                        </div>
                        <CardDescription>Your next scheduled match</CardDescription>
                    </CardHeader>
                    <CardContent class="relative z-10 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-muted-foreground">Opponent</p>
                                <p class="font-semibold">Naomi Brown</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Tournament</p>
                                <p class="font-semibold">Australia Open</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-1">
                                <CalendarDays class="h-4 w-4" />
                                <span>Jan 18, 2026</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <Clock class="h-4 w-4" />
                                <span>2:00 PM</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <MapPin class="h-4 w-4" />
                                <span>Court 1</span>
                            </div>
                        </div>
                        <Button class="w-full">View Details</Button>
                    </CardContent>
                </Card>

                <!-- Recent Match -->
                <Card class="relative overflow-hidden">
                    <div class="absolute inset-0 z-0">
                        <img
                            src="https://images.unsplash.com/photo-1595435742656-5272d0b3fa82?w=600&h=400&fit=crop"
                            alt="Tennis player"
                            class="h-full w-full object-cover opacity-20"
                        />
                        <div class="absolute inset-0 bg-gradient-to-r from-background/95 to-background/90"></div>
                    </div>
                    <CardHeader class="relative z-10">
                        <div class="flex items-center justify-between">
                            <CardTitle class="text-xl">Recent Match</CardTitle>
                            <span class="rounded-full bg-accent px-3 py-1 text-xs font-medium text-accent-foreground">WON</span>
                        </div>
                        <CardDescription>Your last completed match</CardDescription>
                    </CardHeader>
                    <CardContent class="relative z-10 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-muted-foreground">Opponent</p>
                                <p class="font-semibold">Emma Wilson</p>
                            </div>
                            <div>
                                <p class="text-sm text-muted-foreground">Score</p>
                                <p class="font-semibold">6-3, 3-6, 6-2</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-sm">
                            <div class="flex items-center gap-1">
                                <CalendarDays class="h-4 w-4" />
                                <span>Jan 15, 2026</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <Clock class="h-4 w-4" />
                                <span>45 mins</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <MapPin class="h-4 w-4" />
                                <span>Court 3</span>
                            </div>
                        </div>
                        <Button variant="outline" class="w-full">View Match Report</Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
