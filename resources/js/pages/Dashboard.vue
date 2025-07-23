<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { useInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { CalendarDays } from 'lucide-vue-next';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Get authenticated user
const page = usePage();
const authenticatedUser = page.props.auth.user as User;
const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(() => authenticatedUser.avatar && authenticatedUser.avatar !== '');

// Mock data for the dashboard - keeping some for demo purposes
const user = {
    name: authenticatedUser.name,
    username: '@' + authenticatedUser.name.toLowerCase().replace(/\s+/g, '.'),
    avatar: authenticatedUser.avatar,
    country: 'Indonesia',
    age: 27,
    birth: '24 - 02 - 1998',
    sex: 'Women',
    wta: 10,
    followers: 190,
    following: 550,
    friends: 1869,
};

const nextMatch = {
    date: '18 JANUARY 2026',
    opponent: {
        name: 'Naomi Brown',
        country: 'Japan',
        avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop&crop=face',
    },
    venue: 'vs',
};

const latestScores = {
    tournament: 'WTA - SINGLES',
    event: 'Australia Open, hard',
    category: 'SINGLES',
    status: 'WON',
    scores: [
        { set: 1, player: 6, opponent: 3 },
        { set: 2, player: 3, opponent: 6 },
        { set: 3, player: 3, opponent: 2 },
    ],
};

const stats = {
    totalWins: 224,
    winPercentage: 68.5,
    singlesWins: 86,
    doublesWins: 100,
    mixedDoublesWins: 38,
};

const headToHeadData = [
    {
        opponent: {
            name: 'Serena Williams',
            country: 'USA',
            avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop&crop=face',
        },
        wins: 15,
        losses: 8,
        totalMatches: 23,
        winPercentage: 65.2,
        lastMatch: 'W',
    },
    {
        opponent: {
            name: 'Naomi Osaka',
            country: 'Japan',
            avatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b977?w=200&h=200&fit=crop&crop=face',
        },
        wins: 12,
        losses: 6,
        totalMatches: 18,
        winPercentage: 66.7,
        lastMatch: 'L',
    },
    {
        opponent: {
            name: 'Emma Raducanu',
            country: 'GBR',
            avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop&crop=face',
        },
        wins: 9,
        losses: 7,
        totalMatches: 16,
        winPercentage: 56.3,
        lastMatch: 'W',
    },
    {
        opponent: {
            name: 'Iga Swiatek',
            country: 'Poland',
            avatar: 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?w=200&h=200&fit=crop&crop=face',
        },
        wins: 8,
        losses: 6,
        totalMatches: 14,
        winPercentage: 57.1,
        lastMatch: 'W',
    },
    {
        opponent: {
            name: 'Coco Gauff',
            country: 'USA',
            avatar: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=200&h=200&fit=crop&crop=face',
        },
        wins: 7,
        losses: 5,
        totalMatches: 12,
        winPercentage: 58.3,
        lastMatch: 'L',
    },
];

const courtReservations = [
    {
        date: '2025-07-25',
        time: '14:00 - 15:30',
        court: 'Court A',
        status: 'upcoming',
        type: 'Singles Practice',
        opponent: null,
    },
    {
        date: '2025-07-23',
        time: '16:00 - 17:30',
        court: 'Court B',
        status: 'completed',
        type: 'Match',
        opponent: 'Emma Wilson',
    },
    {
        date: '2025-07-28',
        time: '10:00 - 11:30',
        court: 'Court C',
        status: 'upcoming',
        type: 'Doubles Training',
        opponent: 'Mixed Doubles',
    },
    {
        date: '2025-07-21',
        time: '18:00 - 19:30',
        court: 'Court A',
        status: 'completed',
        type: 'Practice',
        opponent: null,
    },
    {
        date: '2025-07-30',
        time: '15:00 - 16:30',
        court: 'Court D',
        status: 'upcoming',
        type: 'Tournament Match',
        opponent: 'Sarah Chen',
    },
];

const recentMatches = [
    {
        date: '2025-07-20',
        opponent: 'Serena Williams',
        tournament: 'Wimbledon',
        result: 'W',
        score: '6-4, 3-6, 6-2',
        avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop&crop=face',
    },
    {
        date: '2025-07-15',
        opponent: 'Naomi Osaka',
        tournament: 'French Open',
        result: 'L',
        score: '4-6, 6-3, 4-6',
        avatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b977?w=200&h=200&fit=crop&crop=face',
    },
    {
        date: '2025-07-10',
        opponent: 'Emma Raducanu',
        tournament: 'US Open',
        result: 'W',
        score: '6-2, 6-1',
        avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&h=200&fit=crop&crop=face',
    },
    {
        date: '2025-07-05',
        opponent: 'Iga Swiatek',
        tournament: 'Australian Open',
        result: 'W',
        score: '7-5, 6-4',
        avatar: 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?w=200&h=200&fit=crop&crop=face',
    },
    {
        date: '2025-07-01',
        opponent: 'Coco Gauff',
        tournament: 'Miami Open',
        result: 'L',
        score: '3-6, 6-4, 5-7',
        avatar: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=200&h=200&fit=crop&crop=face',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Welcome Section -->
            <div class="flex flex-col gap-6 lg:flex-row">
                <!-- Welcome Card -->
                <div class="flex-1">
                    <Card class="relative overflow-hidden">
                        <!-- Background Image -->
                        <div class="absolute inset-0 z-0">
                            <img
                                src="https://images.unsplash.com/photo-1622547748225-3fc4abd2cca0?w=800&h=400&fit=crop&crop=center"
                                alt="Tennis court background"
                                class="h-full w-full object-cover opacity-10"
                            />
                            <div class="absolute inset-0 bg-gradient-to-r from-background/95 to-background/90"></div>
                        </div>
                        <CardHeader class="relative z-10 pb-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <CardTitle class="text-2xl font-bold">Welcome {{ user.name }}!</CardTitle>
                                    <CardDescription class="text-sm text-muted-foreground"> TODAY IS SATURDAY, 11TH NOVEMBER 2025 </CardDescription>
                                </div>
                                <div class="flex gap-6 text-sm">
                                    <div class="text-center">
                                        <div class="font-semibold">{{ user.followers }}K</div>
                                        <div class="text-muted-foreground">FOLLOWERS</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-semibold">{{ user.following }}</div>
                                        <div class="text-muted-foreground">FOLLOWING</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="font-semibold">{{ user.friends }}</div>
                                        <div class="text-muted-foreground">FRIENDS</div>
                                    </div>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent class="relative z-10 flex items-center gap-6">
                            <!-- User Profile -->
                            <div class="flex items-center gap-4">
                                <Avatar class="h-24 w-24 border-4 border-white/20">
                                    <AvatarImage v-if="showAvatar" :src="authenticatedUser.avatar!" :alt="authenticatedUser.name" />
                                    <AvatarFallback class="text-xl font-semibold">{{ getInitials(authenticatedUser.name) }}</AvatarFallback>
                                </Avatar>
                                <div>
                                    <h3 class="text-xl font-bold">{{ user.name }}</h3>
                                    <p class="mb-2 text-sm text-muted-foreground">{{ user.country }}</p>
                                    <div class="space-y-1 text-sm">
                                        <div><span class="font-medium">Age:</span> {{ user.age }}</div>
                                        <div><span class="font-medium">Birth:</span> {{ user.birth }}</div>
                                        <div><span class="font-medium">Sex:</span> {{ user.sex }}</div>
                                        <div><span class="font-medium">WTA:</span> {{ user.wta }}</div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- My Next Match -->
                <Card class="relative w-full overflow-hidden lg:w-96">
                    <!-- Background Image -->
                    <div class="absolute inset-0 z-0">
                        <img
                            src="https://images.unsplash.com/photo-1542144582-1ba00456b5e3?w=400&h=300&fit=crop&crop=center"
                            alt="Tennis match background"
                            class="h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                    </div>
                    <CardHeader class="relative z-10 flex flex-row items-center justify-between">
                        <CardTitle class="text-lg text-white">My Next Match</CardTitle>
                        <div class="flex items-center gap-1 text-sm text-white/80">
                            <CalendarDays class="h-4 w-4" />
                            {{ nextMatch.date }}
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div class="text-center">
                                <Avatar class="mx-auto mb-2 h-16 w-16 border-2 border-white/50">
                                    <AvatarImage
                                        src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop&crop=face"
                                        :alt="nextMatch.opponent.name"
                                    />
                                    <AvatarFallback class="bg-white/20 text-white">{{
                                        nextMatch.opponent.name
                                            .split(' ')
                                            .map((n) => n[0])
                                            .join('')
                                    }}</AvatarFallback>
                                </Avatar>
                                <div class="font-semibold text-white">{{ nextMatch.opponent.name }}</div>
                                <div class="text-sm text-white/70">{{ nextMatch.opponent.country }}</div>
                            </div>
                            <div class="text-2xl font-bold text-white/90">VS</div>
                            <div class="text-center">
                                <Avatar class="mx-auto mb-2 h-16 w-16 border-2 border-white/50">
                                    <AvatarImage
                                        src="https://images.unsplash.com/photo-1494790108755-2616b612b977?w=150&h=150&fit=crop&crop=face"
                                        :alt="user.name"
                                    />
                                    <AvatarFallback class="bg-white/20 text-white">{{
                                        user.name
                                            .split(' ')
                                            .map((n) => n[0])
                                            .join('')
                                    }}</AvatarFallback>
                                </Avatar>
                                <div class="font-semibold text-white">{{ user.name }}</div>
                                <div class="text-sm text-white/70">{{ user.country }}</div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Stats and Scores Section -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Recent Matches -->
                <Card class="relative overflow-hidden">
                    <!-- Background Image -->
                    <div class="absolute inset-0 z-0">
                        <img
                            src="https://images.unsplash.com/photo-1544717301-9cdcb1f5940f?w=1200&h=400&fit=crop&crop=center"
                            alt="Tennis matches background"
                            class="h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-r from-background/98 via-background/90 to-background/95"></div>
                    </div>
                    <CardHeader class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="text-lg">Recent Matches</CardTitle>
                                <CardDescription class="text-sm text-muted-foreground">Last 5 matches played</CardDescription>
                            </div>
                            <Button variant="ghost" size="sm">VIEW MORE</Button>
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="space-y-3">
                            <div
                                v-for="(match, index) in recentMatches"
                                :key="index"
                                class="flex items-center justify-between rounded-lg border bg-card/50 p-3 backdrop-blur-sm"
                            >
                                <div class="flex items-center gap-3">
                                    <Avatar class="h-8 w-8">
                                        <AvatarImage :src="match.avatar" :alt="match.opponent" />
                                        <AvatarFallback class="text-xs">{{ getInitials(match.opponent) }}</AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <div class="text-sm font-semibold">{{ match.opponent }}</div>
                                        <div class="text-xs text-muted-foreground">
                                            {{ match.tournament }} • {{ new Date(match.date).toLocaleDateString() }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <div class="text-xs text-muted-foreground">Score</div>
                                        <div class="font-mono text-sm">{{ match.score }}</div>
                                    </div>
                                    <Badge :variant="match.result === 'W' ? 'default' : 'destructive'" class="h-5 w-6 justify-center text-xs">
                                        {{ match.result }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="relative overflow-hidden">
                    <!-- Background Image -->
                    <div class="absolute inset-0 z-0">
                        <img
                            src="https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=1200&h=400&fit=crop&crop=center"
                            alt="Tennis rivalry background"
                            class="h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-r from-background/98 via-background/90 to-background/95"></div>
                    </div>
                    <CardHeader class="relative z-10">
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="text-lg">Head to Head</CardTitle>
                                <CardDescription class="text-sm text-muted-foreground">Top rivalries</CardDescription>
                            </div>
                            <Button variant="ghost" size="sm">VIEW ALL</Button>
                        </div>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="space-y-3">
                            <div
                                v-for="(rivalry, index) in headToHeadData.slice(0, 5)"
                                :key="index"
                                class="flex items-center justify-between rounded-lg border bg-card/50 p-3 backdrop-blur-sm"
                            >
                                <!-- Opponent Info -->
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-muted-foreground">#{{ index + 1 }}</span>
                                    <Avatar class="h-8 w-8">
                                        <AvatarImage :src="rivalry.opponent.avatar" :alt="rivalry.opponent.name" />
                                        <AvatarFallback class="text-xs">{{ getInitials(rivalry.opponent.name) }}</AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <div class="text-sm font-semibold">{{ rivalry.opponent.name }}</div>
                                        <div class="text-xs text-muted-foreground">{{ rivalry.opponent.country }}</div>
                                    </div>
                                </div>

                                <!-- Win/Loss Record -->
                                <div class="flex items-center gap-4">
                                    <div class="text-center">
                                        <div class="text-xs text-muted-foreground">W-L</div>
                                        <div class="text-sm font-bold text-green-600">{{ rivalry.wins }}-{{ rivalry.losses }}</div>
                                    </div>
                                    <Badge :variant="rivalry.lastMatch === 'W' ? 'default' : 'destructive'" class="h-5 w-6 justify-center text-xs">
                                        {{ rivalry.lastMatch }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Performance Cards -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <Card class="relative overflow-hidden">
                    <!-- Background Image -->
                    <div class="absolute inset-0 z-0">
                        <img
                            src="https://images.unsplash.com/photo-1595435742656-5272d0b3fa82?w=400&h=250&fit=crop&crop=center"
                            alt="Tennis performance background"
                            class="h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-r from-background/98 via-background/90 to-background/95"></div>
                    </div>
                    <CardHeader class="relative z-10">
                        <CardTitle class="text-lg">Performance Summary</CardTitle>
                    </CardHeader>
                    <CardContent class="relative z-10">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <Badge variant="secondary" class="mb-2 bg-white/90 text-black">SINGLES</Badge>
                                <div class="text-2xl font-bold">{{ stats.singlesWins }}</div>
                                <div class="text-xs text-muted-foreground">Wins</div>
                            </div>
                            <div class="text-center">
                                <Badge variant="secondary" class="mb-2 bg-white/90 text-black">DOUBLES</Badge>
                                <div class="text-2xl font-bold">{{ stats.doublesWins }}</div>
                                <div class="text-xs text-muted-foreground">Wins</div>
                            </div>
                            <div class="text-center">
                                <Badge variant="secondary" class="mb-2 bg-white/90 text-black">MIXED</Badge>
                                <div class="text-2xl font-bold">{{ stats.mixedDoublesWins }}</div>
                                <div class="text-xs text-muted-foreground">Wins</div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle class="text-lg">Court Reservations</CardTitle>
                                <CardDescription class="text-sm text-muted-foreground">Upcoming & recent bookings</CardDescription>
                            </div>
                            <Button variant="ghost" size="sm">VIEW ALL</Button>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <div
                                v-for="(reservation, index) in courtReservations.slice(0, 5)"
                                :key="index"
                                class="flex items-center justify-between rounded-lg border p-3"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-muted">
                                        <span class="text-xs font-semibold">{{ reservation.court.split(' ')[1] }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold">{{ reservation.type }}</div>
                                        <div class="text-xs text-muted-foreground">
                                            {{ new Date(reservation.date).toLocaleDateString() }} • {{ reservation.time }}
                                        </div>
                                        <div v-if="reservation.opponent" class="text-xs text-muted-foreground">vs {{ reservation.opponent }}</div>
                                    </div>
                                </div>
                                <Badge :variant="reservation.status === 'upcoming' ? 'default' : 'secondary'" class="text-xs">
                                    {{ reservation.status }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
