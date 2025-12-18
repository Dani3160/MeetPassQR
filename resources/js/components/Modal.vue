<template>
    <Transition name="modal">
        <div v-if="show" class="modal-overlay" @click.self="handleClose">
            <div class="modal-container" :class="size">
                <div class="modal-header" v-if="title || showClose">
                    <h2 v-if="title" class="modal-title">{{ title }}</h2>
                    <button v-if="showClose" @click="handleClose" class="modal-close" aria-label="Close">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <slot></slot>
                </div>
                <div class="modal-footer" v-if="$slots.footer">
                    <slot name="footer"></slot>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script>
export default {
    name: 'Modal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        title: {
            type: String,
            default: ''
        },
        size: {
            type: String,
            default: 'medium',
            validator: (value) => ['small', 'medium', 'large', 'xl'].includes(value)
        },
        closeOnOverlay: {
            type: Boolean,
            default: true
        },
        showClose: {
            type: Boolean,
            default: true
        }
    },
    watch: {
        show(newVal) {
            if (newVal) {
                // Prevent body scroll and horizontal overflow
                document.body.classList.add('modal-open');
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
                document.body.style.maxWidth = '100%';
                document.documentElement.style.overflowX = 'hidden';
            } else {
                // Restore body scroll
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.position = '';
                document.body.style.width = '';
                document.body.style.maxWidth = '';
                document.documentElement.style.overflowX = '';
            }
        }
    },
    methods: {
        handleClose() {
            if (this.closeOnOverlay) {
                this.$emit('close');
            }
        }
    },
    beforeUnmount() {
        // Clean up body styles
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.position = '';
        document.body.style.width = '';
        document.body.style.maxWidth = '';
        document.documentElement.style.overflowX = '';
    }
}
</script>

<style scoped>
@import '../styles/modal.css';
</style>
