<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ArrowRight } from 'lucide-vue-next';
</script>

<template>
    <!-- CTA Section -->
    <section class="bg-muted py-24">
        <div class="mx-auto max-w-4xl px-6 text-center lg:px-8">
            <h2 class="text-4xl font-bold tracking-tight text-foreground">Ready to book your next game?</h2>
            <p class="mt-4 text-lg text-muted-foreground">
                Join thousands of players already booking courts and tracking their progress with Skvosh.
            </p>
            <div class="mt-8 flex items-center justify-center gap-4">
                <Button size="lg" class="group">
                    Book a Court Now
                    <ArrowRight class="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                </Button>
                <Button variant="outline" size="lg"> Browse Clubs </Button>
            </div>
        </div>
    </section>
</template>
