<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Search',
        href: '/search',
    },
];
</script>

<template>
    <Head title="Search" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold">Search</h1>
                <p class="text-muted-foreground">Find players, matches, and tournaments</p>
            </div>
        </div>
    </AppLayout>
</template>
