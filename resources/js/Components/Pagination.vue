<template>
    <div v-if="links.length > 0">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, linkIndex) in links" :key="linkIndex">
                <div
                    v-if="link.url === null"
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded"
                    v-html="replaceLabel(link.label)"   
                >
                </div>
                <Link
                    v-else
                    :href="link.url"
                    :class="[
                        'mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded inline-block',
                        link.active ? 'bg-indigo-500 text-white' : 'hover:bg-white focus:border-indigo-500 focus:text-indigo-500'
                    ]"
                >
                    <span v-html="replaceLabel(link.label)"></span>
                </Link>
            </template>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
const props = defineProps({
    links: Array
})

// Replace English labels with Bahasa Melayu
function replaceLabel(label) {
    if (label === '&laquo; Previous') return '&laquo; Sebelumnya'
    if (label === 'Next &raquo;') return 'Seterusnya &raquo;'
    return label
}
</script>
