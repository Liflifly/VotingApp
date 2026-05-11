import { ref } from 'vue';

/**
 * useNeoConfirm — promise-based confirm dialog composable.
 *
 * 1. Add <NeoConfirm v-bind="confirmProps" /> to your component template.
 * 2. Call: const ok = await neoConfirm({ title, message, variant, confirmLabel })
 *
 * Example:
 *   const { confirmProps, neoConfirm } = useNeoConfirm();
 *   const ok = await neoConfirm({ title: 'Delete?', message: 'This cannot be undone.', variant: 'danger' });
 *   if (ok) { ... }
 */
export function useNeoConfirm() {
  const confirmProps = ref({
    modelValue:      false,
    title:           'Are you sure?',
    message:         'This action cannot be undone.',
    confirmLabel:    'CONFIRM',
    variant:         'danger',
    requireCheckbox: false,
    checkboxLabel:   'I understand that this action is permanent and cannot be undone.',
  });

  let _resolve = null;

  const neoConfirm = ({ title, message, variant = 'danger', confirmLabel, requireCheckbox = false, checkboxLabel } = {}) => {
    confirmProps.value = {
      modelValue:      true,
      title:           title        ?? 'Are you sure?',
      message:         message      ?? 'This action cannot be undone.',
      confirmLabel:    confirmLabel ?? (variant === 'danger' ? 'DELETE' : 'CONFIRM'),
      variant,
      requireCheckbox,
      checkboxLabel:   checkboxLabel ?? 'I understand that this action is permanent and cannot be undone.',
    };

    return new Promise((resolve) => {
      _resolve = resolve;
    });
  };

  const onConfirm = () => {
    confirmProps.value.modelValue = false;
    _resolve?.(true);
    _resolve = null;
  };

  const onCancel = () => {
    confirmProps.value.modelValue = false;
    _resolve?.(false);
    _resolve = null;
  };

  return { confirmProps, neoConfirm, onConfirm, onCancel };
}
