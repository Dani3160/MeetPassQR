<template>
    <div class="date-picker-wrapper">
        <input
            ref="dateInput"
            type="text"
            :placeholder="placeholder"
            :value="displayValue"
            :class="['form-control date-input', { 'is-invalid': error }]"
            :disabled="disabled"
            readonly
        />
    </div>
</template>

<script>
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

// Indonesian locale configuration
const Indonesian = {
    weekdays: {
        shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
        longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
    },
    months: {
        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
    },
    firstDayOfWeek: 1,
    rangeSeparator: ' sampai ',
    weekAbbreviation: 'Mgg',
    scrollTitle: 'Scroll untuk memperbesar',
    toggleTitle: 'Klik untuk mengubah',
    amPM: ['AM', 'PM'],
    yearAriaLabel: 'Tahun',
    monthAriaLabel: 'Bulan',
    hourAriaLabel: 'Jam',
    minuteAriaLabel: 'Menit',
    time_24hr: false
};

export default {
    name: 'DatePicker',
    props: {
        modelValue: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: 'Pilih tanggal'
        },
        minDate: {
            type: String,
            default: null
        },
        maxDate: {
            type: String,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        },
        error: {
            type: Boolean,
            default: false
        },
        dateFormat: {
            type: String,
            default: 'Y-m-d'
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            flatpickrInstance: null,
            displayValue: ''
        };
    },
    watch: {
        modelValue(newVal) {
            if (this.flatpickrInstance) {
                const currentValue = this.flatpickrInstance.input.value;
                if (newVal !== currentValue) {
                    this.flatpickrInstance.setDate(newVal, false);
                    this.displayValue = newVal || '';
                }
            } else {
                this.displayValue = newVal || '';
            }
        }
    },
    mounted() {
        this.initFlatpickr();
    },
    beforeUnmount() {
        if (this.flatpickrInstance) {
            this.flatpickrInstance.destroy();
        }
    },
    methods: {
        initFlatpickr() {
            const options = {
                dateFormat: this.dateFormat,
                locale: Indonesian,
                minDate: this.minDate || null,
                maxDate: this.maxDate || null,
                disableMobile: true,
                allowInput: false,
                clickOpens: true,
                closeOnSelect: true,
                onChange: (selectedDates, dateStr, instance) => {
                    // Update display value immediately
                    this.displayValue = dateStr;
                    // Emit the updated value
                    this.$emit('update:modelValue', dateStr);
                    // Close the calendar immediately after selection
                    instance.close();
                },
                onReady: (selectedDates, dateStr, instance) => {
                    if (this.modelValue) {
                        instance.setDate(this.modelValue, false);
                        this.displayValue = this.modelValue;
                    } else {
                        this.displayValue = '';
                    }
                },
                onOpen: () => {
                    // Ensure display value is synced when calendar opens
                    if (this.modelValue && this.flatpickrInstance) {
                        this.displayValue = this.modelValue;
                    }
                }
            };

            this.flatpickrInstance = flatpickr(this.$refs.dateInput, options);
            
            if (this.modelValue) {
                this.flatpickrInstance.setDate(this.modelValue, false);
                this.displayValue = this.modelValue;
            }
        }
    }
};
</script>

<style scoped>
@import '../styles/date-picker.css';
</style>
