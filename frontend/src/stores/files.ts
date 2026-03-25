import { ref } from 'vue'
import { defineStore } from 'pinia'
import {
  listFiles,
  uploadFiles as apiUploadFiles,
  downloadFile as apiDownloadFile,
  deleteFile as apiDeleteFile,
} from '@/api/files'
import type { PaginationMeta, UserFile } from '@/types/file'

const PER_PAGE = 15

export const useFilesStore = defineStore('files', () => {
  const files = ref<UserFile[]>([])
  const meta = ref<PaginationMeta | null>(null)
  const search = ref('')
  const currentPage = ref(1)
  const loading = ref(false)
  const uploading = ref(false)
  const fileToDelete = ref<UserFile | null>(null)
  const error = ref<string | null>(null)

  async function fetchFiles(): Promise<void> {
    loading.value = true
    error.value = null
    try {
      const response = await listFiles({
        page: currentPage.value,
        per_page: PER_PAGE,
        search: search.value || undefined,
      })
      files.value = response.data
      meta.value = response.meta
    } catch {
      error.value = 'Failed to load files.'
    } finally {
      loading.value = false
    }
  }

  async function uploadFiles(pendingFiles: File[]): Promise<void> {
    uploading.value = true
    error.value = null
    try {
      await apiUploadFiles(pendingFiles)
      currentPage.value = 1
      await fetchFiles()
    } catch {
      error.value = 'Upload failed. Check file types and sizes.'
    } finally {
      uploading.value = false
    }
  }

  async function downloadFile(file: UserFile): Promise<void> {
    error.value = null
    try {
      await apiDownloadFile(file.id, file.name)
    } catch {
      error.value = 'Download failed.'
    }
  }

  function confirmDelete(file: UserFile): void {
    fileToDelete.value = file
  }

  function cancelDelete(): void {
    fileToDelete.value = null
  }

  async function executeDelete(): Promise<void> {
    if (!fileToDelete.value) return
    error.value = null
    try {
      await apiDeleteFile(fileToDelete.value.id)
      fileToDelete.value = null
      await fetchFiles()
    } catch {
      error.value = 'Delete failed.'
      fileToDelete.value = null
    }
  }

  function setSearch(query: string): void {
    search.value = query
    currentPage.value = 1
    fetchFiles()
  }

  function setPage(page: number): void {
    currentPage.value = page
    fetchFiles()
  }

  return {
    files,
    meta,
    search,
    currentPage,
    loading,
    uploading,
    fileToDelete,
    error,
    fetchFiles,
    uploadFiles,
    downloadFile,
    confirmDelete,
    cancelDelete,
    executeDelete,
    setSearch,
    setPage,
  }
})
