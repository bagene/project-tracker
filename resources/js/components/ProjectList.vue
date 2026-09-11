<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import type { Project, ApiPaginatedResponse } from '@/types';
import ProjectForm from './ProjectForm.vue';

const projects = ref<Project[]>([]);
const loading = ref(true);
const showForm = ref(false);
const editingProject = ref<Project | null>(null);

const search = ref('');
const status = ref('');
const priority = ref('');
const sortBy = ref('created_at');
const sortDir = ref('desc');

const fetchProjects = async () => {
    loading.value = true;
    try {
        const response = await axios.get<ApiPaginatedResponse<Project>>('/api/projects', {
            params: {
                search: search.value,
                status: status.value,
                priority: priority.value,
                sort_by: sortBy.value,
                sort_dir: sortDir.value,
            }
        });
        projects.value = response.data.data;
    } catch (error) {
        console.error('Failed to fetch projects:', error);
    } finally {
        loading.value = false;
    }
};

watch([search, status, priority, sortBy, sortDir], () => {
    fetchProjects();
});

const deleteProject = async (id: number) => {
    if (!confirm('Are you sure you want to delete this project?')) return;

    try {
        await axios.delete(`/api/projects/${id}`);
        projects.value = projects.value.filter(p => p.id !== id);
    } catch (error) {
        console.error('Failed to delete project:', error);
    }
};

const openCreateForm = () => {
    editingProject.value = null;
    showForm.value = true;
};

const openEditForm = (project: Project) => {
    editingProject.value = project;
    showForm.value = true;
};

const handleFormSaved = () => {
    showForm.value = false;
    fetchProjects();
};

onMounted(fetchProjects);
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Projects</h1>
            <button
                @click="openCreateForm"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Add Project
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search projects..."
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select
                        v-model="status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                        <option value="">All Statuses</option>
                        <option value="Planning">Planning</option>
                        <option value="In Progress">In Progress</option>
                        <option value="On Hold">On Hold</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Priority</label>
                    <select
                        v-model="priority"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                        <option value="">All Priorities</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Sort By</label>
                    <select
                        v-model="sortBy"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                        <option value="created_at">Date Created</option>
                        <option value="project_name">Project Name</option>
                        <option value="client_name">Client Name</option>
                        <option value="due_date">Due Date</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Order</label>
                    <select
                        v-model="sortDir"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    >
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </div>
            </div>
        </div>

        <div v-if="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <p class="mt-2 text-gray-500">Loading projects...</p>
        </div>

        <div v-else-if="projects.length === 0" class="bg-white shadow rounded-lg p-12 text-center">
            <p class="text-gray-500">No projects found. Create your first project!</p>
        </div>

        <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                <li v-for="project in projects" :key="project.id">
                    <div class="px-4 py-4 sm:px-6 hover:bg-gray-50 transition duration-150 ease-in-out">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <p class="text-sm font-medium text-indigo-600 truncate">
                                    {{ project.project_name }}
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ project.client_name }}
                                </p>
                            </div>
                            <div class="ml-2 flex-shrink-0 flex items-center space-x-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="{
                                        'bg-green-100 text-green-800': project.status === 'Completed',
                                        'bg-blue-100 text-blue-800': project.status === 'In Progress',
                                        'bg-yellow-100 text-yellow-800': project.status === 'Planning',
                                        'bg-red-100 text-red-800': project.status === 'On Hold'
                                    }">
                                    {{ project.status }}
                                </span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="{
                                        'bg-red-100 text-red-800': project.priority === 'High',
                                        'bg-orange-100 text-orange-800': project.priority === 'Medium',
                                        'bg-gray-100 text-gray-800': project.priority === 'Low'
                                    }">
                                    {{ project.priority }}
                                </span>
                            </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between items-center">
                            <div class="sm:flex text-sm text-gray-500 space-x-4">
                                <div>
                                    Due: {{ project.due_date }}
                                </div>
                                <div v-if="project.description" class="hidden md:block italic">
                                    {{ project.description }}
                                </div>
                            </div>
                            <div class="mt-2 flex items-center space-x-2 sm:mt-0">
                                <button
                                    @click="openEditForm(project)"
                                    class="text-indigo-600 hover:text-indigo-900 font-medium text-sm"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteProject(project.id)"
                                    class="text-red-600 hover:text-red-900 font-medium text-sm"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Modal -->
        <div v-if="showForm" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showForm = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ editingProject ? 'Edit Project' : 'Add New Project' }}
                        </h3>
                        <div class="mt-4">
                            <ProjectForm
                                :project="editingProject"
                                @saved="handleFormSaved"
                                @cancel="showForm = false"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
