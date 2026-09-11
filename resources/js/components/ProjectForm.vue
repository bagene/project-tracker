<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { ProjectStatus, ProjectPriority } from '@/types';
import type { Project, ProjectFormData } from '@/types';

const props = defineProps<{
    project?: Project | null;
}>();

const emit = defineEmits(['saved', 'cancel']);

const formData = ref<ProjectFormData>({
    client_name: '',
    project_name: '',
    description: '',
    status: ProjectStatus.PLANNING,
    priority: ProjectPriority.MEDIUM,
    start_date: new Date().toISOString().split('T')[0],
    due_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
});

const errors = ref<Record<string, string[]>>({});
const saving = ref(false);

onMounted(() => {
    if (props.project) {
        formData.value = {
            client_name: props.project.client_name,
            project_name: props.project.project_name,
            description: props.project.description || '',
            status: props.project.status,
            priority: props.project.priority,
            start_date: props.project.start_date,
            due_date: props.project.due_date,
        };
    }
});

const submit = async () => {
    saving.value = true;
    errors.value = {};

    try {
        if (props.project) {
            await axios.put(`/api/projects/${props.project.id}`, formData.value);
        } else {
            await axios.post('/api/projects', formData.value);
        }
        emit('saved');
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error('Failed to save project:', error);
        }
    } finally {
        saving.value = false;
    }
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Client Name</label>
            <input
                v-model="formData.client_name"
                type="text"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                required
            >
            <p v-if="errors.client_name" class="mt-1 text-sm text-red-600">{{ errors.client_name[0] }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Project Name</label>
            <input
                v-model="formData.project_name"
                type="text"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                required
            >
            <p v-if="errors.project_name" class="mt-1 text-sm text-red-600">{{ errors.project_name[0] }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea
                v-model="formData.description"
                rows="3"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            ></textarea>
            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select
                    v-model="formData.status"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                    <option v-for="status in Object.values(ProjectStatus)" :key="status" :value="status">
                        {{ status }}
                    </option>
                </select>
                <p v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status[0] }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Priority</label>
                <select
                    v-model="formData.priority"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                >
                    <option v-for="priority in Object.values(ProjectPriority)" :key="priority" :value="priority">
                        {{ priority }}
                    </option>
                </select>
                <p v-if="errors.priority" class="mt-1 text-sm text-red-600">{{ errors.priority[0] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                <input
                    v-model="formData.start_date"
                    type="date"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                >
                <p v-if="errors.start_date" class="mt-1 text-sm text-red-600">{{ errors.start_date[0] }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                <input
                    v-model="formData.due_date"
                    type="date"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                >
                <p v-if="errors.due_date" class="mt-1 text-sm text-red-600">{{ errors.due_date[0] }}</p>
            </div>
        </div>

        <div class="mt-5 sm:mt-6 flex justify-end space-x-3">
            <button
                type="button"
                @click="$emit('cancel')"
                class="inline-flex justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Cancel
            </button>
            <button
                type="submit"
                :disabled="saving"
                class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
            >
                {{ saving ? 'Saving...' : 'Save Project' }}
            </button>
        </div>
    </form>
</template>
