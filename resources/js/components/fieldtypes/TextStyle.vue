<script setup>
import { computed, ref } from 'vue';
import { Fieldtype } from '@statamic/cms';

const emit = defineEmits(Fieldtype.emits);
const props = defineProps(Fieldtype.props);
const { expose, update, defineReplicatorPreview } = Fieldtype.use(emit, props);

defineExpose(expose);

const defaults = computed(() => props.meta.defaults || {
    size: 0,
    height: 0,
    width: 0,
});

const limits = computed(() => props.meta.limits || {});
const labels = computed(() => props.meta.labels || {});
const isExpanded = ref(false);

const currentValue = computed(() => ({
    size: Number(props.value?.size ?? defaults.value.size ?? 0),
    height: Number(props.value?.height ?? defaults.value.height ?? 0),
    width: Number(props.value?.width ?? defaults.value.width ?? 0),
}));

const rows = computed(() => ([
    {
        key: 'size',
        shortLabel: 'Size',
        label: labels.value.size || 'Text size',
        help: 'Smaller or larger',
    },
    {
        key: 'height',
        shortLabel: 'Spacing',
        label: labels.value.height || 'Line spacing',
        help: 'Tighter or looser lines',
    },
    {
        key: 'width',
        shortLabel: 'Width',
        label: labels.value.width || 'Text width',
        help: 'Narrower or wider text',
    },
]));

const allDefaults = computed(() => rows.value.every((row) => currentValue.value[row.key] === (defaults.value[row.key] ?? 0)));
const summaryRows = computed(() => rows.value.map((row) => ({
    ...row,
    amount: currentValue.value[row.key],
    active: currentValue.value[row.key] !== (defaults.value[row.key] ?? 0),
})));

defineReplicatorPreview(() => {
    if (allDefaults.value) {
        return 'Default text controls';
    }

    return rows.value
        .map((row) => {
            const amount = currentValue.value[row.key];
            if (!amount) {
                return null;
            }

            return `${row.label} ${amount > 0 ? '+' : ''}${amount}`;
        })
        .filter(Boolean)
        .join(' | ');
});

function clampValue(key, nextValue) {
    const { min, max } = getLimits(key);

    return Math.min(max, Math.max(min, nextValue));
}

function setValue(key, nextValue) {
    update({
        ...currentValue.value,
        [key]: clampValue(key, nextValue),
    });
}

function adjustValue(key, amount) {
    setValue(key, currentValue.value[key] + amount);
}

function resetValue(key) {
    setValue(key, defaults.value[key] ?? 0);
}

function resetAll() {
    update({ ...defaults.value });
}

function getLimits(key) {
    const fieldLimits = limits.value[key] || {};

    return {
        min: Number(fieldLimits.min ?? -6),
        max: Number(fieldLimits.max ?? 12),
        step: Number(fieldLimits.step ?? 1),
    };
}

function getStep(key) {
    return getLimits(key).step;
}

function canAdjust(key, direction) {
    const { min, max } = getLimits(key);
    const nextValue = currentValue.value[key] + (getStep(key) * direction);

    return nextValue >= min && nextValue <= max;
}

function statusText(amount) {
    if (amount === 0) {
        return 'Default';
    }

    return amount > 0 ? `+${amount}` : `${amount}`;
}
</script>

<template>
    <div
        class="text-style-fieldtype"
        :data-expanded="isExpanded"
    >
        <div class="text-style-fieldtype__header">
            <div class="text-style-fieldtype__summary">
                <div
                    v-for="row in summaryRows"
                    :key="row.key"
                    class="text-style-fieldtype__chip"
                    :data-active="row.active"
                >
                    <span>{{ row.shortLabel }}</span>
                    <strong>{{ statusText(row.amount) }}</strong>
                </div>
            </div>

            <div class="text-style-fieldtype__header-actions">
                <button
                    type="button"
                    class="text-style-fieldtype__toggle"
                    :aria-expanded="isExpanded"
                    @click="isExpanded = !isExpanded"
                >
                    {{ isExpanded ? 'Hide controls' : 'Adjust text' }}
                </button>

                <button
                    v-if="!allDefaults"
                    type="button"
                    class="text-style-fieldtype__reset-all"
                    @click="resetAll"
                >
                    Reset all
                </button>
            </div>
        </div>

        <div
            v-if="isExpanded"
            class="text-style-fieldtype__grid"
        >
            <div
                v-for="row in rows"
                :key="row.key"
                class="text-style-fieldtype__card"
            >
                <div class="text-style-fieldtype__copy">
                    <div class="text-style-fieldtype__eyebrow">{{ row.shortLabel }}</div>
                    <div class="text-style-fieldtype__label">{{ row.label }}</div>
                    <div class="text-style-fieldtype__help">{{ row.help }}</div>
                </div>

                <div class="text-style-fieldtype__controls">
                    <button
                        type="button"
                        class="text-style-fieldtype__stepper"
                        :disabled="!canAdjust(row.key, -1)"
                        @click="adjustValue(row.key, -getStep(row.key))"
                    >
                        -
                    </button>

                    <div class="text-style-fieldtype__value">
                        {{ statusText(currentValue[row.key]) }}
                    </div>

                    <button
                        type="button"
                        class="text-style-fieldtype__stepper"
                        :disabled="!canAdjust(row.key, 1)"
                        @click="adjustValue(row.key, getStep(row.key))"
                    >
                        +
                    </button>

                    <button
                        type="button"
                        class="text-style-fieldtype__reset"
                        :disabled="currentValue[row.key] === (defaults[row.key] ?? 0)"
                        @click="resetValue(row.key)"
                    >
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
