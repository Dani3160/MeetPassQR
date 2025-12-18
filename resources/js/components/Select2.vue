<template>
    <select
        ref="selectElement"
        :value="modelValue"
        :class="selectClass"
        :disabled="disabled"
        :multiple="multiple"
    >
        <slot></slot>
    </select>
</template>

<script>
// Import Select2 CSS
import 'select2/dist/css/select2.min.css';

export default {
    name: 'Select2',
    props: {
        modelValue: {
            type: [String, Number, Array],
            default: null
        },
        options: {
            type: Object,
            default: () => ({})
        },
        disabled: {
            type: Boolean,
            default: false
        },
        multiple: {
            type: Boolean,
            default: false
        },
        selectClass: {
            type: String,
            default: 'form-control'
        }
    },
    emits: ['update:modelValue', 'change'],
    mounted() {
        this.initSelect2();
    },
    watch: {
        modelValue(newVal) {
            // Wait for next tick to ensure Select2 is initialized
            this.$nextTick(() => {
            if (this.select2Instance && this.$refs.selectElement) {
                const $ = window.$ || window.jQuery;
                if (!$) return;
                const $element = $(this.$refs.selectElement);
                const currentVal = $element.val();
                // Only update if value changed to prevent infinite loop
                if (String(currentVal) !== String(newVal)) {
                    $element.val(newVal).trigger('change.select2');
                }
                } else if (this.$refs.selectElement && !this.select2Instance) {
                    // If Select2 not initialized yet but element exists, retry init
                    setTimeout(() => {
                        if (!this.select2Instance) {
                            this.initSelect2();
                        }
                    }, 100);
                }
            });
        },
        disabled(newVal) {
            if (this.select2Instance && this.$refs.selectElement) {
                const $ = window.$ || window.jQuery;
                if (!$) return;
                const $element = $(this.$refs.selectElement);
                if (newVal) {
                    $element.prop('disabled', true);
                } else {
                    $element.prop('disabled', false);
                }
            }
        }
    },
    beforeUnmount() {
        this.destroySelect2();
    },
    methods: {
        initSelect2() {
            // Wait for next tick to ensure DOM is ready
            this.$nextTick(() => {
                if (!this.$refs.selectElement) {
                    return;
                }

                // Get jQuery from window (should be available from bootstrap.js)
                const $ = window.$ || window.jQuery;
                
                if (!$) {
                    // Retry after a short delay
                    setTimeout(() => this.initSelect2(), 200);
                    return;
                }

                const $element = $(this.$refs.selectElement);
                
                // Check if select2 is available on jQuery
                if (typeof $.fn.select2 !== 'function') {
                    // First check if Select2 is being loaded from bootstrap
                    if (window.select2Loaded === false) {
                        // Wait for bootstrap to finish loading
                        let retries = 0;
                        const maxRetries = 20;
                        const checkInterval = setInterval(() => {
                            retries++;
                            if (typeof $.fn.select2 === 'function') {
                                clearInterval(checkInterval);
                                setTimeout(() => this.initSelect2(), 50);
                            } else if (window.select2Loaded === true && typeof $.fn.select2 !== 'function') {
                                // Bootstrap says it's loaded but it's not attached
                                clearInterval(checkInterval);
                                this.forceLoadSelect2();
                            } else if (retries >= maxRetries) {
                                clearInterval(checkInterval);
                                this.forceLoadSelect2();
                            }
                        }, 100);
                    } else {
                        // Try to load Select2 dynamically
                        this.forceLoadSelect2();
                    }
                    return;
                }
                
                // Default options
                const defaultOptions = {
                    theme: 'default',
                    width: '100%',
                    language: {
                        noResults: () => 'Tidak ada hasil',
                        searching: () => 'Mencari...'
                    },
                    ...this.options
                };
                
                try {
                    // Initialize Select2
                    $element.select2(defaultOptions);
                    this.select2Instance = $element;
                    
                    // Set initial value - handle both string and number
                    if (this.modelValue !== null && this.modelValue !== undefined && this.modelValue !== '') {
                        // Convert to string for comparison, but keep original type for Select2
                        $element.val(this.modelValue).trigger('change.select2');
                    }
                    
                    // Listen for changes
                    $element.on('change.select2', (e) => {
                        const value = $element.val();
                        this.$emit('update:modelValue', value);
                        this.$emit('change', value, e);
                    });
                } catch (err) {
                    // Error initializing Select2
                    console.error('Select2 initialization error:', err);
                }
            });
        },
        forceLoadSelect2() {
            // Try to load Select2 dynamically and manually attach it
            import('select2/dist/js/select2.js').then((module) => {
                const $ = window.$ || window.jQuery;
                
                // Store module for manual initialization
                window.select2Module = module;
                
                // Try to manually call factory function
                if (typeof module === 'function') {
                    const result = module(window, $);
                    if (result && typeof result.fn === 'object') {
                        window.jQuery = result;
                        window.$ = result;
                    }
                } else if (module.default && typeof module.default === 'function') {
                    const result = module.default(window, $);
                    if (result && typeof result.fn === 'object') {
                        window.jQuery = result;
                        window.$ = result;
                    }
                }
                
                // Wait for UMD wrapper to execute or manual init to take effect
                let retries = 0;
                const maxRetries = 20;
                const checkInterval = setInterval(() => {
                    retries++;
                    const $check = window.$ || window.jQuery;
                    if ($check && typeof $check.fn.select2 === 'function') {
                        clearInterval(checkInterval);
                        window.select2Loaded = true;
                        setTimeout(() => this.initSelect2(), 50);
                    } else if (retries >= maxRetries) {
                        clearInterval(checkInterval);
                    }
                }, 100);
            }).catch(() => {
                // Failed to dynamically load Select2
            });
        },
        destroySelect2() {
            if (this.select2Instance && this.$refs.selectElement) {
                const $ = window.$ || window.jQuery;
                if (!$) return;
                $(this.$refs.selectElement).off('change.select2');
                $(this.$refs.selectElement).select2('destroy');
                this.select2Instance = null;
            }
        }
    }
}
</script>

<style scoped>
@import '../styles/select2.css';
</style>
