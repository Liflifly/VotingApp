const fs = require('fs');
const path = require('path');

const layouts = `import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';`;
const baseTpl = (name, script) => `<template>
    <AuthenticatedLayout title="${name}">
        <div>
            <!-- Claude akan mengganti UI ini -->
            <p>Ini adalah halaman ${name}. Komponen ini menyediakan data dan form dari Inertia.</p>
            <slot />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
${layouts}
${script}
</script>
`;

const files = {
  'resources/js/Pages/Admin/Elections/Index.vue': baseTpl('Kelola Periode', `import { Link } from '@inertiajs/vue3';\ndefineProps({ elections: Array });`),
  'resources/js/Pages/Admin/Elections/Create.vue': baseTpl('Tambah Periode', `import { useForm } from '@inertiajs/vue3';\nconst form = useForm({ name: '', starts_at: '', ends_at: '', notes: '' });\nconst submit = () => form.post(route('admin.elections.store'));`),
  'resources/js/Pages/Admin/Elections/Edit.vue': baseTpl('Edit Periode', `import { useForm } from '@inertiajs/vue3';\nconst props = defineProps({ election: Object });\nconst form = useForm({ name: props.election.name, starts_at: props.election.starts_at, ends_at: props.election.ends_at, notes: props.election.notes });\nconst submit = () => form.put(route('admin.elections.update', props.election.id));`),
  'resources/js/Pages/Admin/Elections/Show.vue': baseTpl('Detail Periode', `defineProps({ election: Object, totalVoters: Number });`),
  'resources/js/Pages/Admin/Elections/History.vue': baseTpl('Riwayat Periode', `defineProps({ elections: Array });`),
  'resources/js/Pages/Admin/Election/Edit.vue': baseTpl('Jadwal Pemilihan', `import { useForm } from '@inertiajs/vue3';\nconst props = defineProps({ election: Object });\nconst form = useForm({ name: props.election?.name || '', starts_at: props.election?.starts_at || '', ends_at: props.election?.ends_at || '' });\nconst submit = () => form.put(route('admin.election.update'));`),
  'resources/js/Pages/Admin/Candidates/Index.vue': baseTpl('Kelola Kandidat', `import { Link } from '@inertiajs/vue3';\ndefineProps({ election: Object, candidates: Array });`),
  'resources/js/Pages/Admin/Candidates/Create.vue': baseTpl('Tambah Kandidat', `import { useForm } from '@inertiajs/vue3';\nconst props = defineProps({ election: Object });\nconst form = useForm({ name: '', order_number: '', class: '', vision: '', mission: '', program: '', photo: null });\nconst submit = () => form.post(route('admin.candidates.store', props.election.id));`),
  'resources/js/Pages/Admin/Candidates/Edit.vue': baseTpl('Edit Kandidat', `import { useForm, router } from '@inertiajs/vue3';\nconst props = defineProps({ election: Object, candidate: Object });\nconst form = useForm({ name: props.candidate.name, order_number: props.candidate.order_number, class: props.candidate.class, vision: props.candidate.vision, mission: props.candidate.mission, program: props.candidate.program, _method: 'put', photo: null });\nconst submit = () => { form.post(route('admin.candidates.update', {election: props.election.id, candidate: props.candidate.id})) };`),
  'resources/js/Pages/Admin/Results/Index.vue': baseTpl('Hasil (Admin)', `import { Link } from '@inertiajs/vue3';\ndefineProps({ results: Array, totalVoters: Number, totalVotes: Number, elections: Array, selectedElection: Object });`),
  'resources/js/Pages/Admin/Users/Index.vue': baseTpl('Kelola Admin', `import { useForm } from '@inertiajs/vue3';\ndefineProps({ users: Array });\nconst updateRole = (user, newRole) => { const form = useForm({ role: newRole }); form.put(route('admin.users.updateRole', user.id)); };`),
};

for (const [filepath, content] of Object.entries(files)) {
  const dir = path.dirname(filepath);
  if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
  fs.writeFileSync(filepath, content);
}
console.log('All files created!');
