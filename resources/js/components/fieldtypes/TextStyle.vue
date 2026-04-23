<script setup>
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';
import { Button } from '@statamic/cms/ui';

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

const currentValue = computed(() => ({
    size: Number(props.value?.size ?? defaults.value.size ?? 0),
    height: Number(props.value?.height ?? defaults.value.height ?? 0),
    width: Number(props.value?.width ?? defaults.value.width ?? 0),
}));

const rows = computed(() => ([
    {
        key: 'size',
        label: labels.value.size || 'Text size',
        help: 'Make this text smaller or larger.',
    },
    {
        key: 'height',
        label: labels.value.height || 'Line spacing',
        help: 'Tighten or loosen the space between lines.',
    },
    {
        key: 'width',
        label: labels.value.width || 'Text width',
        help: 'Make the text block narrower or wider.',
    },
]));

const allDefaults = computed(() => rows.value.every((row) => currentValue.value[row.key] === (defaults.value[row.key] ?? 0)));

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
    const fieldLimits = limits.value[key] || {};
    const min = Number(fieldLimits.min ?? -6);
    const max = Number(fieldLimits.max ?? 12);

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

function statusText(amount) {
    if (amount === 0) {
        return 'Default';
    }

    return amount > 0 ? `+${amount}` : `${amount}`;
}
</script>

<template>
    <div class="text-style-fieldtype">
        <div
            v-for="row in rows"
            :key="row.key"
            class="text-style-fieldtype__row"
        >
            <div class="text-style-fieldtype__copy">
                <div class="text-style-fieldtype__label">{{ row.label }}</div>
                <div class="text-style-fieldtype__help">{{ row.help }}</div>
            </div>

            <div class="text-style-fieldtype__controls">
                <Button
                    size="sm"
                    class="text-style-fieldtype__button"
                    @click.prevent="adjustValue(row.key, -1)"
                >
                    -
                </Button>

                <div class="text-style-fieldtype__value">
                    {{ statusText(currentValue[row.key]) }}
                </div>

                <Button
                    size="sm"
                    class="text-style-fieldtype__button"
                    @click.prevent="adjustValue(row.key, 1)"
                >
                    +
                </Button>

                <button
                    type="button"
                    class="text-style-fieldtype__reset"
                    :disabled="currentValue[row.key] === (defaults[row.key] ?? 0)"
                    @click.prevent="resetValue(row.key)"
                >
                    Reset
                </button>
            </div>
        </div>

        <div class="text-style-fieldtype__footer">
            <p>Use the buttons to fine-tune the text. Reset brings it back to the original design.</p>
            <button
                type="button"
                class="text-style-fieldtype__reset-all"
                :disabled="allDefaults"
                @click.prevent="resetAll"
            >
                Reset all to default
            </button>
        </div>
    </div>
</template>
