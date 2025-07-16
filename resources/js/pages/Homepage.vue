<script setup lang="ts">
import CallToActionSection from '@/components/homepage/CallToActionSection.vue';
import FeaturesSection from '@/components/homepage/FeaturesSection.vue';
import FooterSection from '@/components/homepage/FooterSection.vue';
import HeroSection from '@/components/homepage/HeroSection.vue';
import NavigationBar from '@/components/homepage/NavigationBar.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Skvosh - Sports Ranking Platform">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-background via-background to-muted">
        <NavigationBar :auth="$page.props.auth" :app-name="$page.props?.name" />
        <HeroSection :auth="$page.props.auth" />
    </div>

    <FeaturesSection />
    <CallToActionSection />
    <FooterSection />
</template>
