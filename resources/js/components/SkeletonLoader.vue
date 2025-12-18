<template>
    <div class="skeleton-loader" :class="type">
        <div v-if="type === 'card'" class="skeleton-card">
            <div class="skeleton-header"></div>
            <div class="skeleton-line" style="width: 60%"></div>
            <div class="skeleton-line" style="width: 80%"></div>
            <div class="skeleton-line" style="width: 40%"></div>
        </div>
        <div v-else-if="type === 'table'" class="skeleton-table">
            <div class="skeleton-row" v-for="i in rows" :key="i">
                <div class="skeleton-cell" v-for="j in cols" :key="j" :style="{ width: cellWidths[j-1] || '100%' }"></div>
            </div>
        </div>
        <div v-else-if="type === 'list'" class="skeleton-list">
            <div class="skeleton-item" v-for="i in rows" :key="i">
                <div class="skeleton-avatar"></div>
                <div class="skeleton-content">
                    <div class="skeleton-line" style="width: 70%"></div>
                    <div class="skeleton-line" style="width: 50%"></div>
                </div>
            </div>
        </div>
        <div v-else-if="type === 'grid'" class="skeleton-grid">
            <div class="skeleton-card" v-for="i in rows" :key="i">
                <div class="skeleton-image"></div>
                <div class="skeleton-line" style="width: 80%"></div>
                <div class="skeleton-line" style="width: 60%"></div>
            </div>
        </div>
        <div v-else class="skeleton-default">
            <div class="skeleton-line" v-for="i in rows" :key="i" :style="{ width: widths[i-1] || '100%' }"></div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'SkeletonLoader',
    props: {
        type: {
            type: String,
            default: 'default',
            validator: (value) => ['default', 'card', 'table', 'list', 'grid'].includes(value)
        },
        rows: {
            type: Number,
            default: 3
        },
        cols: {
            type: Number,
            default: 4
        },
        cellWidths: {
            type: Array,
            default: () => []
        },
        widths: {
            type: Array,
            default: () => []
        }
    }
}
</script>

<style scoped>
@import '../styles/skeleton-loader.css';
</style>
