<template>
    <div class="form-group">
        <label :for="fieldId" class="form-label">
            {{ field.field_label }}
            <span v-if="field.is_required" class="text-danger">*</span>
        </label>
        
        <!-- Input -->
        <input
            v-if="field.field_type === 'input'"
            :id="fieldId"
            :type="inputType"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="field.field_placeholder"
            :required="field.is_required"
            class="form-control"
        />
        
        <!-- Textarea -->
        <textarea
            v-else-if="field.field_type === 'textarea'"
            :id="fieldId"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="field.field_placeholder"
            :required="field.is_required"
            class="form-control"
            rows="3"
        ></textarea>
        
        <!-- Select -->
        <Select2
            v-else-if="field.field_type === 'select'"
            :key="`select-${field.id_field}-${normalizedModelValue || 'empty'}`"
            :model-value="normalizedModelValue"
            @update:model-value="$emit('update:modelValue', $event)"
            :options="{
                placeholder: field.field_placeholder || `Pilih ${field.field_label}`,
                allowClear: !field.is_required
            }"
            :disabled="false"
        >
            <option value="">{{ field.field_placeholder || `Pilih ${field.field_label}` }}</option>
            <option
                v-for="option in fieldOptions"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </Select2>
        
        <!-- Radio -->
        <div v-else-if="field.field_type === 'radio'" class="radio-group">
            <label
                v-for="option in fieldOptions"
                :key="option.value"
                class="radio-label"
            >
                <input
                    type="radio"
                    :name="fieldId"
                    :value="option.value"
                    :checked="modelValue === option.value"
                    @change="$emit('update:modelValue', $event.target.value)"
                    :required="field.is_required"
                />
                <span>{{ option.label }}</span>
            </label>
        </div>
        
        <!-- Checkbox -->
        <div v-else-if="field.field_type === 'checkbox'" class="checkbox-group">
            <label
                v-for="option in fieldOptions"
                :key="option.value"
                class="checkbox-label"
            >
                <input
                    type="checkbox"
                    :value="option.value"
                    :checked="isChecked(option.value)"
                    @change="handleCheckboxChange($event, option.value)"
                />
                <span>{{ option.label }}</span>
            </label>
        </div>
        
        <!-- File -->
        <div v-else-if="field.field_type === 'file'" class="file-input-wrapper">
            <input
                :id="fieldId"
                type="file"
                @change="handleFileChange"
                :required="field.is_required && !modelValue"
                class="form-control"
            />
            <div v-if="modelValue && typeof modelValue === 'string'" class="file-preview">
                <span>File: {{ getFileName(modelValue) }}</span>
                <button type="button" @click="clearFile" class="btn-remove-file">Hapus</button>
            </div>
        </div>
        
        <div v-if="error" class="text-danger mt-1" style="font-size: 0.875rem;">{{ error }}</div>
    </div>
</template>

<script>
import Select2 from './Select2.vue';

export default {
    name: 'DynamicFormField',
    components: {
        Select2
    },
    props: {
        field: {
            type: Object,
            required: true
        },
        modelValue: {
            type: [String, Number, Array, File],
            default: null
        },
        error: {
            type: String,
            default: null
        }
    },
    emits: ['update:modelValue'],
    computed: {
        fieldId() {
            return `field_${this.field.id_field}`;
        },
        fieldOptions() {
            if (!this.field.field_options || !this.field.field_options.options) {
                return [];
            }
            return this.field.field_options.options;
        },
        normalizedModelValue() {
            // For select fields, normalize value to match option values (convert to string)
            if (this.field.field_type === 'select' && this.modelValue !== null && this.modelValue !== undefined && this.modelValue !== '') {
                const valueStr = String(this.modelValue);
                // Check if value exists in options - try both string and original type
                const option = this.fieldOptions.find(opt => {
                    const optValueStr = String(opt.value);
                    return optValueStr === valueStr || String(opt.value) === String(this.modelValue);
                });
                if (option) {
                    // Return the exact option value (preserve original type if possible)
                    // But ensure it matches what's in the option
                    return option.value;
                }
                // If not found in options, still return as string
                // This handles cases where database value doesn't match any option
                return valueStr;
            }
            return this.modelValue;
        },
        inputType() {
            const validation = this.field.field_validation || {};
            if (validation.type === 'email') return 'email';
            if (validation.type === 'number') return 'number';
            if (validation.type === 'tel') return 'tel';
            return 'text';
        }
    },
    methods: {
        isChecked(value) {
            if (!this.modelValue) {
                return false;
            }
            if (Array.isArray(this.modelValue)) {
                return this.modelValue.includes(value);
            }
            return this.modelValue === value;
        },
        handleCheckboxChange(event, value) {
            let currentValues = Array.isArray(this.modelValue) 
                ? [...this.modelValue] 
                : (this.modelValue ? [this.modelValue] : []);
            
            if (event.target.checked) {
                if (!currentValues.includes(value)) {
                    currentValues.push(value);
                }
            } else {
                currentValues = currentValues.filter(v => v !== value);
            }
            
            this.$emit('update:modelValue', currentValues);
        },
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.$emit('update:modelValue', file);
            }
        },
        getFileName(filePath) {
            if (typeof filePath === 'string') {
                return filePath.split('/').pop();
            }
            return '';
        },
        clearFile() {
            this.$emit('update:modelValue', null);
            const input = document.getElementById(this.fieldId);
            if (input) {
                input.value = '';
            }
        }
    }
}
</script>

<style scoped>
@import '../styles/dynamic-form-field.css';
</style>
